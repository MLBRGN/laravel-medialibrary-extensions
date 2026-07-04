@if(config('medialibrary-extensions.debug') && !app()->environment('production'))
    <div class="mle-debug-wrapper">
        <div
            class="mle-debug mle-hidden hidden"
            id="{{ $id }}-debug-content"
            data-mle-debug
        >
            <div class="mle-debug-header">
                <h2>📦 Media Library Extensions Debug</h2>

                <div class="mle-debug-badges">
                    <span>Theme: {{ $getConfig('theme') }}</span>
                    <span>DB: {{ DB::getDatabaseName() }}</span>
                </div>
            </div>

            {{-- ========================================================= --}}
            {{-- HEALTH / WARNINGS --}}
            {{-- ========================================================= --}}

            @if(collect($errors)->isNotEmpty())
                <section class="mle-debug-section mle-debug-errors">
                    <h3>⚠️ Warnings</h3>

                    <ul>
                        @foreach($errors as $error)
                            <li>{!! $error !!}</li>
                        @endforeach
                    </ul>
                </section>
            @endif

            {{-- ========================================================= --}}
            {{-- MODEL --}}
            {{-- ========================================================= --}}

            <section class="mle-debug-section">
                <h3>🗄️ Model</h3>

                <table class="mle-debug-table">
                    <tr>
                        <th>Type</th>
                        <td>{{ $modelType ?? 'n/a' }}</td>
                    </tr>

                    <tr>
                        <th>ID</th>
                        <td>{{ $modelId ?? 'n/a' }}</td>
                    </tr>

                    <tr>
                        <th>Table</th>
                        <td>{{ $model?->getTable() ?? 'n/a' }}</td>
                    </tr>

                    <tr>
                        <th>Connection</th>
                        <td>{{ $model?->getConnection()->getName() ?? 'n/a' }}</td>
                    </tr>

                    <tr>
                        <th>Database</th>
                        <td>{{ $model?->getConnection()->getDatabaseName() ?? 'n/a' }}</td>
                    </tr>
                </table>
            </section>

            {{-- ========================================================= --}}
            {{-- COMPONENT --}}
            {{-- ========================================================= --}}

            <section class="mle-debug-section">
                <h3>⚙️ Component</h3>

                <table class="mle-debug-table">
                    <tr>
                        <th>Component ID</th>
                        <td>{{ $id }}</td>
                    </tr>

                    <tr>
                        <th>Instance ID</th>
                        <td>{{ $getConfig('instanceId') }}</td>
                    </tr>

                    <tr>
                        <th>Client token</th>
                        <td>{{ $getConfig('clientToken') }}</td>
                    </tr>

                    <tr>
                        <th>Frontend Theme</th>
                        <td>{{ $getConfig('theme') }}</td>
                    </tr>

                    <tr>
                        <th>Temporary Upload</th>
                        <td>{{ $getConfig('temporaryUploadMode') ? 'Yes' : 'No' }}</td>
                    </tr>
                </table>
            </section>

            {{-- ========================================================= --}}
            {{-- DATABASE --}}
            {{-- ========================================================= --}}

            <section class="mle-debug-section">
                <h3>💾 Database</h3>

                <table class="mle-debug-table">
                    <tr>
                        <th>Default Connection</th>
                        <td>{{ DB::connection()->getName() }}</td>
                    </tr>

                    <tr>
                        <th>Database Name</th>
                        <td>{{ DB::getDatabaseName() }}</td>
                    </tr>
                </table>
            </section>

            {{-- ========================================================= --}}
            {{-- COLLECTIONS --}}
            {{-- ========================================================= --}}

            <section class="mle-debug-section">
                <h3>🎞️ Collections</h3>

                <table class="mle-debug-table">
                    <thead>
                    <tr>
                        <th>Type</th>
                        <th>Collection</th>
                        <th>Media Count</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($getCollectionDebugData() as $data)
                        <tr>
                            <td>{{ ucfirst($data['type']) }}</td>
                            <td>{{ $data['collection'] }}</td>
                            <td>{{ $data['count'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </section>

            {{-- ========================================================= --}}
            {{-- FEATURES --}}
            {{-- ========================================================= --}}

            <section class="mle-debug-section">
                <h3>🎛️ Features</h3>

                <table class="mle-debug-table">
                    <tr>
                        <th>Destroy Button</th>
                        <td>{{ $getConfig('showDestroyButton') ? 'Enabled' : 'Disabled' }}</td>
                    </tr>

                    <tr>
                        <th>Set As First</th>
                        <td>{{ $getConfig('showSetAsFirstButton') ? 'Enabled' : 'Disabled' }}</td>
                    </tr>

                    <tr>
                        <th>Media Edit</th>
                        <td>{{ $getConfig('showMediaEditButton') ? 'Enabled' : 'Disabled' }}</td>
                    </tr>

                    <tr>
                        <th>Menu</th>
                        <td>{{ $getConfig('showMenu') ? 'Enabled' : 'Disabled' }}</td>
                    </tr>

                    <tr>
                        <th>Ordering</th>
                        <td>{{ $getConfig('showOrder') ? 'Enabled' : 'Disabled' }}</td>
                    </tr>
                </table>
            </section>

            {{-- ========================================================= --}}
            {{-- ROUTES --}}
            {{-- ========================================================= --}}

            <section class="mle-debug-section">
                <h3>🌐 Routes</h3>

                <table class="mle-debug-table">
                    <tr>
                        <th>Media Upload</th>
                        <td><code>{{ $getConfig('routes.mediaUpload') }}</code></td>
                    </tr>

                    <tr>
                        <th>YouTube Upload</th>
                        <td><code>{{ $getConfig('routes.youtubeUpload') }}</code></td>
                    </tr>

                    <tr>
                        <th>Preview Update</th>
                        <td><code>{{ $getConfig('routes.mediaManagerPreviewUpdate') }}</code></td>
                    </tr>

                    <tr>
                        <th>Lab Preview Base Update</th>
                        <td><code>{{ $getConfig('routes.mediaLabPreviewBaseUpdate') }}</code></td>
                    </tr>

                    <tr>
                        <th>Lab Preview Original Update</th>
                        <td><code>{{ $getConfig('routes.mediaLabPreviewOriginalUpdate') }}</code></td>
                    </tr>
                </table>
            </section>

            {{-- ========================================================= --}}
            {{-- CONFIG --}}
            {{-- ========================================================= --}}

            <section class="mle-debug-section">
                <h3>🧩 Package Config</h3>

                <table class="mle-debug-table">
                    <tr>
                        <th>XHR Enabled</th>
                        <td>{{ config('medialibrary-extensions.use_xhr') ? 'Yes' : 'No' }}</td>
                    </tr>

                    <tr>
                        <th>Show Status</th>
                        <td>{{ config('medialibrary-extensions.show_status') ? 'Yes' : 'No' }}</td>
                    </tr>

                    <tr>
                        <th>YouTube Support</th>
                        <td>{{ config('medialibrary-extensions.youtube_support_enabled') ? 'Yes' : 'No' }}</td>
                    </tr>

                    <tr>
                        <th>Allowed Mime Types</th>
                        <td>
                            {{ collect(config('medialibrary-extensions.allowed_mimetypes'))
                                ->flatten()
                                ->join(', ') }}
                        </td>
                    </tr>
                </table>
            </section>

            <details class="mle-debug-section">
                <summary>🗂️ Registered (sub-) components ({{ count($getComponents()) }})</summary>
                
                <div class="mle-debug-components-list">
                    @foreach($getComponents() as $compId => $compData)
                        <details class="mle-debug-component-item">
                            <summary>
                                {{ $compData['name'] }} (ID: {{ $compId }})
                            </summary>
                            
                            <div class="mle-debug-component-data">
                                <strong>Config:</strong>
                                <pre>{{ json_encode($getSanitizedConfig($compData['config']), JSON_PRETTY_PRINT) }}</pre>
                                
                                <strong>Options:</strong>
                                <pre>{{ json_encode(collect($compData['options'])->sortKeys()->all(), JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </details>
                    @endforeach
                </div>
            </details>

            {{-- ========================================================= --}}
            {{-- RAW DUMPS (Main Component) --}}
            {{-- ========================================================= --}}

            <details class="mle-debug-section">
                <summary>🧾 Main Component Config Dump</summary>

                <pre>{{ json_encode($getSanitizedConfig($getConfig()), JSON_PRETTY_PRINT) }}</pre>
            </details>

            <details class="mle-debug-section">
                <summary>🧾 Raw Options Dump</summary>

                <pre>{{ json_encode(collect($getOptions())->sortKeys()->all(), JSON_PRETTY_PRINT) }}</pre>
            </details>

        </div>
    </div>
@endif