<?php

namespace App\Imports;

use App\Models\Voters;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


// Add this if you want to log errors for missing emails
use Illuminate\Support\Facades\Log;

// Make sure WithHeadingRow is added after ToCollection
class VotersImport implements ToCollection, WithHeadingRow
{
   public function collection(Collection $rows)
    {
        Log::info('Starting Voters Import. Rows received: ' . $rows->count());

        foreach ($rows as $index => $row)
        {
            // Convert row to array for consistent logging
            $rowData = is_array($row) ? $row : $row->toArray();
            Log::info('Processing row index: ' . $index, $rowData);

            // --- Check for required email ---
            if (!isset($row['email']) || empty($row['email'])) {
                 Log::warning('Skipping row index ' . $index . ' due to missing or empty email.');
                 continue; // Skip this row
            }

            Log::info('Checking for voter with email: ' . $row['email']);
            $voter = Voters::where('email', $row['email'])->first();

            

            if ($voter) {
                // --- Update Existing Voter ---
                Log::info('Found existing voter (ID: ' . $voter->id . '). Attempting update.');
                try {
                    // Only update fields from your table, remove phone/address
                    $updated = $voter->update([
                        // Use ?? to keep existing value if Excel column is missing/null
                        'voter_id'   => $row['voter_id'] ?? $voter->voter_id,
                        'name'       => $row['name'] ?? $voter->name,
                        'gender'     => $row['gender'] ?? $voter->gender,
                        'role'     => $row['role'] ?? $voter->role,
                        'programme'  => $row['programme'] ?? $voter->programme,
                        'class'      => $row['class'] ?? $voter->class,
                        // 'email' is used for lookup, usually not updated here unless specifically needed
                    ]);
                    Log::info('Update attempt result for ID ' . $voter->id . ': ' . ($updated ? 'Success/Changes Made' : 'No Changes/Failed'));
                } catch (\Exception $e) {
                     Log::error('Error updating voter ID ' . $voter->id . ': ' . $e->getMessage(), ['exception' => $e]);
                }

            } else {
                // --- Create New Voter (Optional Logic) ---
                Log::info('Voter with email ' . $row['email'] . ' not found. Checking create logic.');

                // --- Uncomment and adjust this block IF you want to create new voters ---
                
                Log::info('Attempting to create new voter.');
                try {
                    // Check if essential data for creation exists in the row
                    if (isset($row['voter_id'], $row['name'], $row['gender'], $row['programme'], $row['class'], $row['email'])) {
                        $newVoter = Voters::create([
                            // Assign values from the row, ensure keys match Excel headers (lowercase)
                            'voter_id'   => $row['voter_id'],
                            'name'       => $row['name'],
                            'gender'     => $row['gender'],
                            'role'     => $row['role'],
                            'programme'  => $row['programme'],
                            'class'      => $row['class'],
                            'email'      => $row['email'], // Email is known to exist from the check above
                        ]);
                        Log::info('Successfully created new voter with ID: ' . $newVoter->id);
                    } else {
                         Log::warning('Skipping creation for row index ' . $index . ' due to missing required fields (voter_id, name, gender, department, or email).');
                    }
                } catch (\Exception $e) {
                    // Log errors, especially unique constraint or non-nullable field errors
                    Log::error('Error creating new voter for email ' . $row['email'] . ': ' . $e->getMessage(), ['exception' => $e]);
                }
                
                // --- End of Optional Create Block ---

                // If you ONLY want to update and NOT create, you can leave the 'else' block empty or just log:
                // Log::info('Voter with email ' . $row['email'] . ' not found. Create logic is disabled/not implemented.');
            }
        }
         Log::info('Finished Voters Import.');
    }

    // Default heading row is 1, usually no need to implement this method
    // public function headingRow(): int
    // {
    //     return 1;
    // }
}