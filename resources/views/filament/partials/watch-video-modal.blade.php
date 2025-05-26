<div class="aspect-video w-full">
    @if ($video->video)
        <video controls class="w-full h-auto rounded-lg shadow">
            <source src="{{ Storage::url($video->video) }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    @else
        <p class="text-gray-500">No video uploaded.</p>
    @endif

</div>
