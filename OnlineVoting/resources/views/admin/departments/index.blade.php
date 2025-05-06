@extends('layouts.admin') {{-- Your admin layout --}}

@section('title', 'Manage Departments')

@push('styles')
    {{-- Font Awesome for icons (ensure it's loaded in your main layout or uncomment) --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* --- Basic Reset/Defaults --- */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif; background-color: #f4f7f6; color: #333; }
        a { text-decoration: none; color: #0d6efd; }
        a:hover { text-decoration: underline; }

        /* --- Container & Card --- */
        .departments-container { /* Changed prefix */
            padding: 25px;
        }
        .departments-card { /* Changed prefix */
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.08);
            overflow: hidden; /* Ensures border-radius applies to table */
        }
        .departments-header { /* Changed prefix */
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #eee; /* Separator line */
        }
        .departments-header h1 { /* Changed prefix */
            font-size: 22px;
            font-weight: 600;
            margin: 0;
            color: #333;
        }
        .departments-card-body { /* Changed prefix */
            padding: 0; /* Remove padding if table fills it */
        }

        /* --- Buttons --- */
        .btn { padding: 8px 15px; font-size: 14px; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.2s, box-shadow 0.2s, opacity 0.2s; display: inline-flex; align-items: center; gap: 6px; font-weight: 500; text-decoration: none !important; color: white !important; line-height: 1.5; white-space: nowrap; vertical-align: middle; }
        .btn:hover { opacity: 0.85; box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
        .btn i { margin-right: 5px; }
        .btn-primary { background-color: #0d6efd; } /* Blue */
        .btn-primary:hover { background-color: #0b5ed7; }
        .btn-warning { background-color: #527c57; } /* Greenish Edit */
        .btn-warning:hover { background-color: #4a704f; }
        .btn-danger { background-color: #dc3545; } /* Red Delete */
        .btn-danger:hover { background-color: #bb2d3b; }
        /* Smaller action buttons in table */
        .action-buttons .btn { font-size: 13px; padding: 5px 10px; height: auto; gap: 4px; }
        .action-buttons .btn i { font-size: 0.9em; margin-right: 3px; }

        /* --- Table Styling --- */
        .table { width: 100%; border-collapse: collapse; background-color: #fff; }
        .table th, .table td { padding: 12px 15px; vertical-align: middle; text-align: left; border-bottom: 1px solid #e9ecef; } /* Changed border to bottom only */
        .table thead th {
            background-color: #343a40; /* Dark Header */
            color: #fff; /* White Text */
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom-width: 0; /* Remove double border */
        }
        .table tbody tr:hover { background-color: #f8f9fa; } /* Subtle hover */
        .table .action-buttons { white-space: nowrap; width: 1%; text-align: right; }
        .table .action-buttons form { display: inline-block; margin-left: 6px;}
        .text-center { text-align: center !important; }
        .sl-no { text-align: center; width: 1%; white-space: nowrap; } /* Style SL No column */

        /* --- Alerts --- */
        .alert { position: relative; padding: 1rem 1rem; margin: 0 20px 1rem 20px; border: 1px solid transparent; border-radius: 0.25rem; }
        .alert-success { color: #0f5132; background-color: #d1e7dd; border-color: #badbcc; }
        .alert-danger { color: #842029; background-color: #f8d7da; border-color: #f5c2c7; }

        /* --- Pagination --- */
        .pagination-container { padding: 20px; } /* Add padding around pagination */
        .pagination { display: flex; padding-left: 0; list-style: none; justify-content: center; margin: 0; }
        .page-item .page-link { position: relative; display: block; padding: 0.5rem 0.75rem; margin-left: -1px; line-height: 1.25; color: #0d6efd; background-color: #fff; border: 1px solid #dee2e6; }
        .page-item.active .page-link { z-index: 3; color: #fff; background-color: #0d6efd; border-color: #0d6efd; }
        .page-item.disabled .page-link { color: #6c757d; pointer-events: none; background-color: #fff; border-color: #dee2e6; }
        .page-link:hover { z-index: 2; color: #0a58ca; text-decoration: none; background-color: #e9ecef; border-color: #dee2e6; }

     </style>
@endpush

@section('content')
<div class="departments-container"> {{-- Changed class prefix --}}

    {{-- Session Messages Area --}}
    <div id="messageArea">
         @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
         @endif
         @if (session('error'))
             <div class="alert alert-danger">{{ session('error') }}</div>
         @endif
    </div>

    {{-- Main Card --}}
    <div class="departments-card"> {{-- Changed class prefix --}}
        {{-- Header with Title and Add Button --}}
        <div class="departments-header"> {{-- Changed class prefix --}}
             <h1>Manage Departments</h1>
             <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
                  <i class="fas fa-plus"></i> Add New Department
             </a>
        </div>

        {{-- Table Body --}}
        <div class="departments-card-body"> {{-- Changed class prefix --}}
             <div class="table-responsive">
                 <table class="table">
                     <thead>
                         <tr>
                             <th class="sl-no">Sl. No.</th>
                             <th>Department Name</th>
                             <th style="text-align: right;">Actions</th>
                         </tr>
                     </thead>
                     <tbody>
                         {{-- Use the $departments variable passed from DepartmentController --}}
                         @forelse ($departments as $index => $department)
                             <tr>
                                 <td class="sl-no">{{ $departments->firstItem() + $index }}</td>
                                 <td>{{ $department->name }}</td>
                                  {{-- Display Code if needed
                                 <td>{{ $department->code ?? '--' }}</td>
                                  --}}
                                 <td class="action-buttons">
                                     {{-- Edit Button --}}
                                     <a href="{{ route('admin.departments.edit', $department->id) }}" class="btn btn-warning">
                                         <i class="fas fa-edit"></i> Edit
                                     </a>
                                     {{-- Delete Button --}}
                                     <form action="{{ route('admin.departments.destroy', $department->id) }}" method="POST" onsubmit="return confirm('Are you sure? Deleting this might affect linked Programmes!');">
                                         @csrf
                                         @method('DELETE')
                                         <button type="submit" class="btn btn-danger">
                                             <i class="fas fa-trash"></i> Delete
                                         </button>
                                     </form>
                                 </td>
                             </tr>
                         @empty
                             <tr>
                                 {{-- Adjust colspan if Code column is added --}}
                                 <td colspan="3" class="text-center" style="padding: 20px;">No departments found.</td>
                             </tr>
                         @endforelse
                     </tbody>
                 </table>
             </div>
        </div> {{-- End Card Body --}}
    </div> {{-- End Card --}}

     {{-- Pagination Links --}}
     @if ($departments->hasPages())
     <div class="pagination-container">
          {{ $departments->links() }} {{-- Make sure your AppServiceProvider configures paginator for non-bootstrap --}}
     </div>
     @endif

</div> {{-- End departments-container --}}
@endsection

@push('scripts')
    {{-- No page-specific JavaScript needed for just displaying this list --}}
@endpush