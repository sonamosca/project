<?php

// Namespace should match your file location
// e.g., namespace App\Http\Controllers\Admin; or namespace App\Http\Controllers;
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voter;
use App\Models\Programme;
use App\Imports\VotersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Exception;
use Maatwebsite\Excel\Validators\ValidationException as MaatwebsiteValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View; // Ensure View is imported

class VotersController extends Controller
{
    /**
     * Show the page for importing voters AND manually adding a voter.
     * THIS IS THE METHOD THAT WAS MISSING IN MY LAST FULL CODE SNIPPET.
     */
    public function showImportForm(): View
    {
        $programmes = Programme::orderBy('name')->get(['id', 'name']);
        // Ensure this view path ('admin.voters.import') matches your actual file structure
        return view('admin.voters.import', compact('programmes'));
    }

    /**
     * Handle the import of voter data from an Excel/CSV file.
     */
    public function importExcelData(Request $request): RedirectResponse
    {
        $request->validate([
            'voter_file' => 'required|file|mimes:csv,txt,xls,xlsx|max:10240',
        ]);
        try {
            $import = new VotersImport();
            Excel::import($import, $request->file('voter_file'));

            // Use the correct getter names from VotersImport class
            $successfullyImportedCount = $import->getSuccessfullyCreatedCount();
            $validationSkippedDetails = $import->getValidationSkippedRows();
            $modelCreationFailures = $import->getModelCreationFailureRows();

            $validationSkippedCount = count($validationSkippedDetails);
            $modelCreationFailureCount = count($modelCreationFailures);
            $totalSkippedRows = $validationSkippedCount + $modelCreationFailureCount;

            $messageParts = [];
            if ($successfullyImportedCount > 0) {
                $messageParts[] = "Successfully created {$successfullyImportedCount} voters.";
            }
            if ($totalSkippedRows > 0) {
                 $messageParts[] = "Skipped {$totalSkippedRows} rows due to errors (see details below or logs).";
            }
            if (empty($messageParts)) {
                 $messageParts[] = "No voters were imported or processed. File might be empty or all rows had issues.";
            }
            $message = implode(" ", $messageParts);

            // Prepare session data for detailed errors
            $detailedErrors = array_merge(
                array_map(function ($item) { $item['type'] = 'Validation Error'; return $item; }, $validationSkippedDetails),
                array_map(function ($item) { $item['type'] = 'Database/Model Error'; $item['row_number'] = $item['row_number'] ?? 'Unknown'; return $item; }, $modelCreationFailures)
            );
            if (!empty($detailedErrors)) {
                session()->flash('import_errors_detailed', $detailedErrors);
            }

            Log::info("Voter import process finished.", [
                'successfully_created' => $successfullyImportedCount,
                'rows_skipped_by_validation' => $validationSkippedCount,
                'rows_failed_model_creation' => $modelCreationFailureCount,
                // 'validation_skipped_details' => $validationSkippedDetails, // Optional verbose log
                // 'model_creation_failure_details' => $modelCreationFailures, // Optional verbose log
            ]);

            // Use success/warning/error based on outcome
            $statusType = 'warning'; // Default if something was skipped or nothing happened
            if ($successfullyImportedCount > 0 && $totalSkippedRows == 0) {
                $statusType = 'success';
            } elseif ($successfullyImportedCount == 0 && $totalSkippedRows > 0) {
                $statusType = 'error';
            }

            // Ensure redirect target route name is correct
            return redirect()->route('admin.voters.manage.index')->with($statusType, $message);

        } catch (MaatwebsiteValidationException $e) {
            $failures = $e->failures();
            $simplifiedSkippedInfo = [];
            foreach ($failures as $failure) { $simplifiedSkippedInfo[] = [ /* ... */ ]; }
            Log::error('Voter Import MaatwebsiteValidationException Block.', ['failures' => $failures]); // Log raw failures
            return redirect()->route('admin.voters.manage.index')
                           ->with('error', 'Import failed during initial validation phases.')
                           ->with('import_errors_detailed', $simplifiedSkippedInfo); // Pass simplified for display
        } catch (\Throwable $e) { // Broader catch
            Log::error('General Voter Import Exception in Controller: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('admin.voters.manage.index')->with('error', 'An unexpected error occurred during import: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created voter (manually added).
     */
    public function storeManual(Request $request): RedirectResponse
    {
         // Trim relevant inputs before validation
        $request->merge([
            'email' => trim($request->input('email')),
            'voter_id' => trim($request->input('voter_id')),
            // Add other fields to trim if necessary
        ]);
        $validatedData = $request->validate([
            'voter_id'  => 'required|string|max:100|unique:voters,voter_id',
            'name'      => 'required|string|max:255',
            'gender'    => ['required', Rule::in(['male', 'female'])],
            'email'     => ['required','email','max:255','unique:voters,email','regex:/^\d{8}\.jnec@rub\.edu\.bt$/i'],
            'role'      => ['required', Rule::in(['student', 'staff'])],
            'programme_id' => 'nullable|exists:programmes,id',
        ], [ /* Custom messages */ ]);

        $programmeName = null;
        if (!empty($validatedData['programme_id'])) {
            $programme = Programme::find($validatedData['programme_id']);
            if ($programme) $programmeName = $programme->name;
        }

        Voter::create([
            'voter_id' => $validatedData['voter_id'],
            'name' => $validatedData['name'],
            'gender' => $validatedData['gender'],
            'email' => $validatedData['email'],
            'role' => $validatedData['role'],
            'programme_id' => $validatedData['programme_id'],
            'programme' => $programmeName,
        ]);

        // Ensure redirect target route name is correct
        return redirect()->route('admin.voters.manage.index')
                       ->with('manual_add_success', 'Voter added manually successfully!');
    }
}