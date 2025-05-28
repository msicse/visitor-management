@extends('layouts.app')

@section('title', 'System Diagnostics')

@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-1">
            <h3 class="content-header-title">System Diagnostics</h3>
        </div>
    </div>

    <div class="content-body">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">System Information</h4>
                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                <div class="heading-elements">
                    <ul class="list-inline mb-0">
                        <li><a data-action="reload" id="refresh-btn"><i class="ft-rotate-cw"></i></a></li>
                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="card-content collapse show">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Server Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Operating System</th>
                                    <td>{{ PHP_OS }}</td>
                                </tr>
                                <tr>
                                    <th>PHP Version</th>
                                    <td>{{ PHP_VERSION }}</td>
                                </tr>
                                <tr>
                                    <th>Web Server</th>
                                    <td>{{ $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <th>Server Time</th>
                                    <td>{{ date('Y-m-d H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Database Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Database Type</th>
                                    <td>{{ config('database.connections.' . config('database.default') . '.driver') }}</td>
                                </tr>
                                <tr>
                                    <th>Connection Status</th>
                                    <td>
                                        @try
                                            @php
                                                DB::connection()->getPdo();
                                                $status = true;
                                                $message = 'Connected';
                                            @endphp
                                            <span class="badge badge-success">{{ $message }}</span>
                                        @catch(\Exception $e)
                                            @php
                                                $status = false;
                                                $message = $e->getMessage();
                                            @endphp
                                            <span class="badge badge-danger">Failed</span>
                                            <div class="text-danger">{{ $message }}</div>
                                        @endtry
                                    </td>
                                </tr>
                                @if($status)
                                <tr>
                                    <th>Database Version</th>
                                    <td>
                                        @try
                                            {{ DB::select('SELECT VERSION() as version')[0]->version }}
                                        @catch(\Exception $e)
                                            <span class="badge badge-warning">Unknown</span>
                                        @endtry
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if(PHP_OS !== 'WINNT')
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5>Linux-Specific Diagnostics</h5>
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
                                        <ul class="nav nav-tabs nav-underline nav-justified">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="disk-tab" data-toggle="tab" href="#disk" aria-controls="disk" aria-expanded="true">Disk Space</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="permissions-tab" data-toggle="tab" href="#permissions" aria-controls="permissions" aria-expanded="false">File Permissions</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="processes-tab" data-toggle="tab" href="#processes" aria-controls="processes" aria-expanded="false">Server Processes</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content px-1 pt-1">
                                            <div role="tabpanel" class="tab-pane active" id="disk" aria-labelledby="disk-tab" aria-expanded="true">
                                                <div class="shell-output">
                                                    @php
                                                        exec('df -h 2>&1', $diskOutput);
                                                    @endphp
                                                    <pre>{{ implode("\n", $diskOutput) }}</pre>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="permissions" role="tabpanel" aria-labelledby="permissions-tab" aria-expanded="false">
                                                <div class="shell-output">
                                                    @php
                                                        $uploadPath = public_path('uploads/visitors');
                                                        $permInfo = [];

                                                        if (file_exists($uploadPath)) {
                                                            $permInfo[] = "Upload directory: $uploadPath";
                                                            $permInfo[] = "Permissions: " . substr(sprintf('%o', fileperms($uploadPath)), -4);
                                                            $permInfo[] = "Owner: " . posix_getpwuid(fileowner($uploadPath))['name'];
                                                            $permInfo[] = "Group: " . posix_getgrgid(filegroup($uploadPath))['name'];
                                                            $permInfo[] = "Writable: " . (is_writable($uploadPath) ? 'Yes' : 'No');
                                                        } else {
                                                            $permInfo[] = "Upload directory does not exist: $uploadPath";
                                                        }
                                                    @endphp
                                                    <pre>{{ implode("\n", $permInfo) }}</pre>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="processes" role="tabpanel" aria-labelledby="processes-tab" aria-expanded="false">
                                                <div class="shell-output">
                                                    @php
                                                        exec('ps aux | grep -E "mysql|apache|nginx|php" | grep -v grep 2>&1', $processOutput);
                                                    @endphp
                                                    <pre>{{ implode("\n", $processOutput) }}</pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5>Database Tables Information</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Table Name</th>
                                        <th>Row Count</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @try
                                        @php
                                            $tables = DB::select('SHOW TABLES');
                                            $tablesColumn = 'Tables_in_' . config('database.connections.' . config('database.default') . '.database');
                                        @endphp
                                        @foreach($tables as $table)
                                            @php
                                                $tableName = $table->$tablesColumn;
                                                $count = DB::table($tableName)->count();
                                            @endphp
                                            <tr>
                                                <td>{{ $tableName }}</td>
                                                <td>{{ $count }}</td>
                                                <td>
                                                    @try
                                                        @php
                                                            $status = DB::select("CHECK TABLE `$tableName`")[0];
                                                        @endphp
                                                        @if($status->Msg_text == 'OK')
                                                            <span class="badge badge-success">OK</span>
                                                        @else
                                                            <span class="badge badge-warning">{{ $status->Msg_text }}</span>
                                                        @endif
                                                    @catch(\Exception $e)
                                                        <span class="badge badge-danger">Error</span>
                                                    @endtry
                                                </td>
                                            </tr>
                                        @endforeach
                                    @catch(\Exception $e)
                                        <tr>
                                            <td colspan="3" class="text-center text-danger">
                                                Failed to retrieve table information: {{ $e->getMessage() }}
                                            </td>
                                        </tr>
                                    @endtry
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5>Recent Error Logs</h5>
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="shell-output">
                                            @php
                                                $logPath = storage_path('logs/laravel-' . date('Y-m-d') . '.log');
                                                $logContent = [];

                                                if (file_exists($logPath)) {
                                                    $logContent = array_slice(file($logPath), -20);
                                                } else {
                                                    $logContent[] = "No log file found for today.";
                                                }
                                            @endphp
                                            <pre style="max-height: 300px; overflow-y: auto;">{{ implode("", $logContent) }}</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#refresh-btn').click(function() {
            location.reload();
        });
    });
</script>
<style>
    .shell-output pre {
        background: #f4f4f4;
        border: 1px solid #ddd;
        border-left: 3px solid #4CAF50;
        color: #333;
        page-break-inside: avoid;
        font-family: monospace;
        font-size: 15px;
        line-height: 1.6;
        margin-bottom: 1.6em;
        max-width: 100%;
        overflow: auto;
        padding: 1em 1.5em;
        display: block;
        word-wrap: break-word;
    }
</style>
@endsection
