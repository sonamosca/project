<?php

namespace App\Imports;

use App\Models\Voter; // Use singular Voter model
use App\Models\Programme;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // To access columns by header name
use Maatwebsite\Excel\Concerns\WithValidation; // To apply validation rules per row
use Maatwebsite\Excel\Concerns\SkipsOnFailure;  // To skip rows that fail validation
use Maatwebsite\Excel\Validators\Failure;      // Type for handling validation failures
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithMapping;    // To pre-process data

class VotersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithMapping
{
    use Importable;

    private $successfullyCreatedCount = 0; // Counts actual successful inserts
    private $validationSkippedRows = [];   // Rows failing rules() validation
    private $modelCreationFailureRows = []; // Rows passing rules() but failing in model()

    /**
     * Pre-process each row before validation.
     * Handles type casting and case normalization.
     */
    public function map($row): array
    {
        // Keys accessed ($row['...']) should match your Excel headers after WithHeadingRow processing.
        // Keys returned in the array should match what rules() and model() expect.
        return [
            'voter_id'       => isset($row['voter_id']) ? (string)trim($row['voter_id']) : null, // Cast to string
            'name'           => trim($row['name'] ?? null),
            'gender'         => isset($row['gender']) ? strtolower(trim($row['gender'])) : null, // Convert to lowercase
            'email'          => trim($row['email'] ?? null),
            'role'           => isset($row['role']) ? strtolower(trim($row['role'])) : null,     // Convert to lowercase
            'programme'      => trim($row['programme'] ?? ''), // Get data from 'programme' header, trim
        ];
    }

    /**
    * Process data for each row that passes validation.
    * @param array $row Data processed by map() and validated by rules()
    * @return \App\Models\Voter|null Returns Voter model on success, null on failure
    */
    public function model(array $row)
    {
        $voterId = $row['voter_id'];
        $programmeNameFromFile = $row['programme']; // Use the key returned by map()

        Log::debug("VotersImport::model() - Processing mapped/validated row for voter_id: {$voterId}", $row);

        $programmeId = null;
        $actualProgrammeNameToStoreInVotersTable = null;

        if (!empty($programmeNameFromFile)) {
            // Case-insensitive lookup
            $programme = Programme::whereRaw('LOWER(name) = ?', [strtolower($programmeNameFromFile)])->first();
            if ($programme) {
                $programmeId = $programme->id;
                $actualProgrammeNameToStoreInVotersTable = $programme->name; // Use canonical name from DB
                Log::debug("Programme found: ID {$programmeId}, Name '{$actualProgrammeNameToStoreInVotersTable}'");
            } else {
                // This should be caught by 'programme.exists' validation if $programmeNameFromFile wasn't empty.
                Log::warning("Programme '{$programmeNameFromFile}' for voter '{$voterId}' not found post-validation. programme_id will be null.");
            }
        } else {
            Log::debug("No programme_name provided for voter_id '{$voterId}'.");
        }

        $voterData = [
            'voter_id'     => $voterId,
            'name'         => $row['name'],
            'gender'       => $row['gender'], // Already lowercase
            'email'        => $row['email'],
            'role'         => $row['role'],   // Already lowercase
            'programme'    => $actualProgrammeNameToStoreInVotersTable, // Text name column in voters table
            'programme_id' => $programmeId,                             // Foreign key column
        ];

        try {
            Log::debug("Attempting Voter::create with data: ", $voterData);
            $createdVoter = Voter::create($voterData); // Use singular Voter model name

            if ($createdVoter) {
                $this->successfullyCreatedCount++;
                Log::info("Voter CREATED successfully in DB: voter_id {$voterId}");
                return $createdVoter;
            } else {
                $reason = "Voter::create returned null/false without throwing an exception.";
                Log::error($reason, ['row_data' => $row, 'prepared_data' => $voterData]);
                $this->modelCreationFailureRows[] = ['original_row_data' => $row, 'error' => $reason, 'prepared_data' => $voterData];
                return null;
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $errorMessage = $e->getMessage();
            Log::error("QueryException during Voter::create for voter_id '{$voterId}': " . $errorMessage . " SQL State: " . $e->getCode(), ['row_data' => $row, 'prepared_data' => $voterData]);
            $this->modelCreationFailureRows[] = ['original_row_data' => $row, 'error' => 'Database Query Error: ' . $errorMessage, 'prepared_data' => $voterData];
            return null;
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            Log::error("Generic Throwable during Voter::create for voter_id '{$voterId}': " . $errorMessage, ['row_data' => $row, 'prepared_data' => $voterData]);
            $this->modelCreationFailureRows[] = ['original_row_data' => $row, 'error' => 'General Error: ' . $errorMessage, 'prepared_data' => $voterData];
            return null;
        }
    }

    /**
     * Define validation rules for each row after mapping.
     */
    public function rules(): array
    {
        return [
            'voter_id' => 'required|string|max:100|unique:voters,voter_id', // 'string' validated after map()
            'name' => 'required|string|max:255',
            'gender' => ['required', Rule::in(['male', 'female'])], // Validates lowercased value from map()
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:voters,email',
                'regex:/^\d{8}\.jnec@rub\.edu\.bt$/i' // Specific regex for 8 digits + domain
            ],
            'role' => ['required', Rule::in(['student', 'staff'])], // Validates lowercased value from map()
            'programme' => 'nullable|string|max:255|exists:programmes,name', // Validates 'programme' key from map()
        ];
    }

    /**
     * Custom validation messages.
     */
    public function customValidationMessages()
    {
        return [
            'voter_id.required' => 'Voter ID is required (row :row).',
            'voter_id.unique' => 'Voter ID :input already exists (row :row).',
            'voter_id.string' => 'Voter ID expected as text (row :row).',
            'voter_id.max' => 'Voter ID too long (max 100 chars) (row :row).',
            'name.required' => 'Name is required (row :row).',
            'name.max' => 'Name too long (max 255 chars) (row :row).',
            'gender.required' => 'Gender is required (row :row).',
            'gender.in' => 'Invalid Gender ":input". Allowed: male, female (row :row).',
            'email.required' => 'Email is required (row :row).',
            'email.email' => 'Invalid Email format for ":input" (row :row).',
            'email.unique' => 'Email :input already exists (row :row).',
            'email.regex' => 'Email ":input" must be in the format 12345678.jnec@rub.edu.bt (row :row).',
            'email.max' => 'Email too long (max 255 chars) (row :row).',
            'role.required' => 'Role is required (row :row).',
            'role.in' => 'Invalid Role ":input". Allowed: student, staff (row :row).',
            'programme.exists' => 'Programme name ":input" does not exist (row :row).',
            'programme.string' => 'Programme name must be text (row :row).',
            'programme.max' => 'Programme name too long (max 255 chars) (row :row).',
        ];
    }

    /**
     * Collect details of rows that fail validation defined in rules().
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->validationSkippedRows[] = [
                'row_number' => $failure->row(),
                'attribute'  => $failure->attribute(),
                'errors'     => $failure->errors(),
                'values'     => $failure->values(), // Original values that failed validation
            ];
            Log::warning("Voter import VALIDATION failure at spreadsheet row: " . $failure->row(), [
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'original_values_passed_to_validator' => $failure->values()
            ]);
        }
    }

    // --- Getters for Controller ---
    public function getSuccessfullyCreatedCount(): int
    {
        return $this->successfullyCreatedCount;
    }

    public function getValidationSkippedRows(): array
    {
        return $this->validationSkippedRows;
    }

    public function getModelCreationFailureRows(): array
    {
        return $this->modelCreationFailureRows;
    }
}