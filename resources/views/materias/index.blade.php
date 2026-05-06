@extends('layouts.sidebar')

@section('content')
    <div class="flex flex-col h-full w-full">

        <!-- Header: Botão Adicionar e Barra de Pesquisa -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5 mb-8">

            <!-- Botão: Adicionar Matéria -->
            <x-button id="btnOpenAddModal">
                Adicionar Matéria
                <div class="bg-[#F0E6FA] rounded-full p-1 flex items-center justify-center">
                    <x-icons.plus class="text-[#BEA2E0]" />
                </div>
            </x-button>

            <!-- Input de Pesquisa -->
            <x-search-input class="py-3" />
        </div>

        <!-- Grid de Cards das Matérias -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <x-materia-card nome="Geologia" />
            <x-materia-card nome="Mineração" />
            <x-materia-card nome="Biologia" />
        </div>
    </div>

    <!-- Modal Adicionar Matéria -->
    <x-modals.adicionar-materia />

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btnAdicionar = document.getElementById('btnOpenAddModal');
            const modal = document.getElementById('modalAdicionarMateria');
            const btnCancelar = document.getElementById('btnCancelarMateria');
            const form = document.getElementById('formAdicionarMateria');

            const toggleModal = () => modal.classList.toggle('hidden');

            if (btnAdicionar) btnAdicionar.addEventListener('click', toggleModal);
            if (btnCancelar) btnCancelar.addEventListener('click', toggleModal);

            // Fechar ao clicar fora da caixa principal do modal
            modal.addEventListener('click', (e) => {
                if (e.target === modal) toggleModal();
            });

            // Submit simulado (Mock)
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                alert('Matéria adicionada com sucesso!');
                toggleModal();
                form.reset();
            });
        });
    </script>
@endsection
