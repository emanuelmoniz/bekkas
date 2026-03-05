<x-app-layout>

    @php
        $projectName = optional($project->translation())->name ?? $project->client ?? 'Project';

        $galleryImages = $project->photos
            ->sortByDesc('is_primary')
            ->map(fn ($photo) => [
                'url' => asset('storage/' . $photo->path),
                'original' => $photo->original_path
                    ? asset('storage/' . $photo->original_path)
                    : null,
            ])
            ->values()
            ->toArray();

        $executionHours = $project->execution_time !== null
            ? rtrim(rtrim(number_format((float) $project->execution_time, 2, '.', ''), '0'), '.')
            : null;

        $materialNames = $project->materials
            ->map(fn ($material) => optional($material->translation())->name)
            ->filter()
            ->values();

        $clientUrl = $project->client_url;
        $hasValidClientUrl = is_string($clientUrl)
            && preg_match('/^https?:\/\//i', $clientUrl);
    @endphp

    <div class="py-4">

        {{-- BACK LINK --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-4 flex">
            <a href="{{ route('portfolio.index') }}" class="text-sm text-accent-primary flex items-center gap-1 hover:text-accent-primary/90 no-underline">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                {{ t('portfolio.show.back_to_portfolio') ?: 'Back to portfolio' }}
            </a>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-6 animate-sequence">

            {{-- GALLERY --}}
            <div class="anim-item">
                <x-image-gallery :images="$galleryImages"/>
            </div>

            {{-- DETAILS --}}
            <div class="bg-white p-6 rounded shadow space-y-4 anim-item">

                {{-- NAME --}}
                <h2 class="font-semibold text-xl text-dark">
                    {{ $projectName }}
                </h2>

                @if(optional($project->translation())->description)
                    <p class="text-sm text-grey-dark leading-relaxed">
                        {!! nl2br(e(optional($project->translation())->description)) !!}
                    </p>
                @endif

                <h3 class="text-sm text-grey-dark font-semibold">
                    {{ t('portfolio.show.project_details') ?: 'Project details' }}
                </h3>

                <dl class="space-y-3">
                        <div class="grid grid-cols-[150px_1fr] gap-3 items-baseline">
                            <dt class="text-sm text-grey-dark">{{ t('portfolio.show.production_year') ?: 'Production Year' }}</dt>
                            <dd class="font-medium">{{ $project->production_date?->year ?? '—' }}</dd>
                        </div>

                        <div class="grid grid-cols-[150px_1fr] gap-3 items-baseline">
                            <dt class="text-sm text-grey-dark">{{ t('portfolio.show.execution_time') ?: 'Execution Time' }}</dt>
                            <dd class="font-medium">{{ $executionHours ? $executionHours . 'h' : '—' }}</dd>
                        </div>

                        <div class="grid grid-cols-[150px_1fr] gap-3 items-baseline">
                            <dt class="text-sm text-grey-dark">{{ t('portfolio.show.width') ?: 'Width' }}</dt>
                            <dd class="font-medium">{{ $project->width !== null ? $project->width . ' mm' : '—' }}</dd>
                        </div>

                        <div class="grid grid-cols-[150px_1fr] gap-3 items-baseline">
                            <dt class="text-sm text-grey-dark">{{ t('portfolio.show.length') ?: 'Length' }}</dt>
                            <dd class="font-medium">{{ $project->length !== null ? $project->length . ' mm' : '—' }}</dd>
                        </div>

                        <div class="grid grid-cols-[150px_1fr] gap-3 items-baseline">
                            <dt class="text-sm text-grey-dark">{{ t('portfolio.show.height') ?: 'Height' }}</dt>
                            <dd class="font-medium">{{ $project->height !== null ? $project->height . ' mm' : '—' }}</dd>
                        </div>

                        <div class="grid grid-cols-[150px_1fr] gap-3 items-baseline">
                            <dt class="text-sm text-grey-dark">{{ t('portfolio.show.weight') ?: 'Weight' }}</dt>
                            <dd class="font-medium">{{ $project->weight !== null ? number_format((float) $project->weight, 2) . ' g' : '—' }}</dd>
                        </div>

                        <div class="grid grid-cols-[150px_1fr] gap-3 items-baseline">
                            <dt class="text-sm text-grey-dark">{{ t('portfolio.show.client') ?: 'Client' }}</dt>
                            <dd class="font-medium break-words">
                                @if($project->client)
                                    @if($hasValidClientUrl)
                                        <a href="{{ $project->client_url }}" target="_blank" rel="noopener noreferrer" class="text-accent-primary underline underline-offset-2 hover:text-accent-primary/80">
                                            {{ $project->client }}
                                        </a>
                                    @else
                                        {{ $project->client }}
                                    @endif
                                @else
                                    —
                                @endif
                            </dd>
                        </div>

                        <div class="grid grid-cols-[150px_1fr] gap-3 items-start">
                            <dt class="text-sm text-grey-dark pt-0.5">{{ t('portfolio.show.materials') ?: 'Materials' }}</dt>
                            <dd>
                                @if($materialNames->isNotEmpty())
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($materialNames as $materialName)
                                            <span class="inline-flex bg-grey-light text-grey-dark text-xs font-medium px-2.5 py-1 rounded-full">
                                                {{ $materialName }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="font-medium">—</span>
                                @endif
                            </dd>
                        </div>

                        <div class="grid grid-cols-[150px_1fr] gap-3 items-start">
                            <dt class="text-sm text-grey-dark pt-0.5">{{ t('portfolio.show.categories') ?: 'Categories' }}</dt>
                            <dd>
                                @php
                                    $categoryNames = $project->categories
                                        ->map(fn($c) => optional($c->translation())->name)
                                        ->filter()
                                        ->values();
                                @endphp

                                @if($categoryNames->isNotEmpty())
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($categoryNames as $catName)
                                            <span class="inline-flex bg-grey-light text-grey-dark text-xs font-medium px-2.5 py-1 rounded-full">
                                                {{ $catName }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="font-medium">—</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
    </div>

        {{-- RELATED PROJECTS --}}
        <div class="animate-sequence bg-secondary">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <x-project-slider
                    :categories="$relatedCategories"
                    :excludeProjectIds="[$project->id]"
                    :max="8"
                    order="newest"
                    :title="t('portfolio.show.related_projects') ?: 'Related Projects'"
                />
            </div>
        </div>


</x-app-layout>
