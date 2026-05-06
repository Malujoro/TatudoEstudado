@extends('layouts.sidebar')

@section('content')
    <div class="flex flex-col h-full w-full">

        <!-- Header: Botão Adicionar e Barra de Pesquisa -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5 mb-8">

            <!-- Botão: Adicionar Matéria -->
            <x-button id="btnOpenAddModal">
                Adicionar Matéria
                <div class="bg-[#F0E6FA] rounded-full p-1 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#BEA2E0]" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
            </x-button>

            <!-- Input de Pesquisa -->
            <div class="relative w-full sm:w-[400px]">
                <input type="text" placeholder="Pesquisar"
                    class="w-full bg-[#BEA2E0] placeholder-[#1F1F1F]/80 text-[#1F1F1F] px-5 py-3 rounded-full outline-none focus:ring-2 focus:ring-[#9E82C0] font-medium text-[18px]" />
                <div
                    class="absolute right-3 top-1/2 -translate-y-1/2 bg-[#F0E6FA] rounded-full p-1.5 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#BEA2E0]" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Grid de Cards das Matérias -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <x-materia-card nome="Geologia" />
            <x-materia-card nome="Mineração" />
            <x-materia-card nome="Biologia" />
        </div>
    </div>

    <!-- Modal Adicionar Matéria -->
    <div id="modalAdicionarMateria"
        class="fixed inset-0 z-50 flex items-center justify-center bg-[#1F1F1F]/40 backdrop-blur-sm hidden transition-opacity">
        <div class="bg-[#A084C3] w-full max-w-md rounded-[20px] p-8 shadow-lg relative">
            <h2 class="text-[24px] font-bold text-[#1F1F1F] mb-6">Adicionar Matéria</h2>

            <form id="formAdicionarMateria" class="flex flex-col gap-6">
                <input type="text" placeholder="Nome da Matéria" required
                    class="w-full rounded-full border-none bg-white px-6 py-4 font-semibold text-[#A084C3] placeholder-[#A084C3]/70 outline-none focus:ring-2 focus:ring-[#1F1F1F] text-lg shadow-inner" />

                <div class="flex justify-end gap-6 text-[18px] font-bold text-[#1F1F1F] mt-2">
                    <button type="button" id="btnCancelarMateria"
                        class="hover:opacity-70 transition-opacity">Cancelar</button>
                    <button type="submit" class="hover:opacity-70 transition-opacity">Confirmar</button>
                </div>
            </form>
        </div>
    </div>

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
