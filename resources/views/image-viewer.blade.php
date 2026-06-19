<x-layout :title="$title">
    <style>
        .image-viewer-page {
            width: min(100%, 74rem);
            margin: 0 auto;
            padding: 2rem 1rem 4rem;
            color: #f8fafc;
        }

        .image-viewer-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .image-viewer-title {
            margin: 0;
            color: #f3eadb;
            font-size: clamp(1.45rem, 4vw, 2.25rem);
            font-weight: 900;
        }

        .image-viewer-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.6rem;
            padding: 0.65rem 1rem;
            border-radius: 0.7rem;
            text-decoration: none;
            font-weight: 900;
            color: #0f172a;
            background: #eae0cf;
        }

        .image-viewer-card {
            overflow: hidden;
            border: 1px solid rgba(148, 163, 184, 0.28);
            border-radius: 1rem;
            background: rgba(3, 10, 28, 0.9);
            box-shadow: 0 1.2rem 2.8rem rgba(0, 0, 0, 0.34);
        }

        .image-viewer-card img {
            display: block;
            width: 100%;
            max-height: 78vh;
            object-fit: contain;
            background: #020617;
        }
    </style>

    <section class="image-viewer-page">
        <div class="image-viewer-header">
            <h1 class="image-viewer-title">{{ $title }}</h1>
            <a href="{{ $backUrl }}" class="image-viewer-back">&larr; Back</a>
        </div>

        <div class="image-viewer-card">
            <img src="{{ $src }}" alt="{{ $title }}">
        </div>
    </section>
</x-layout>
