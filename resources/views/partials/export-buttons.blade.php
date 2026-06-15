@php
    // Usage:
    // @include('partials.export-buttons', ['tableId' => 'partenairesTable', 'title' => 'Liste des partenaires', 'filename' => 'partenaires_export'])
@endphp
@if(!isset($tableId))
    @php $tableId = $tableId ?? '' ; $title = $title ?? 'Liste'; $filename = $filename ?? 'export'; @endphp
@endif
<button class="btn btn-outline-danger" onclick="exportToPdf('{{ $tableId }}', '{{ $title }}', '{{ $filename }}')">
    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
</button>
