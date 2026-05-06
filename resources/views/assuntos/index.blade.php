@extends('layouts.sidebar')

@section('content')
    <div class="flex flex-col h-full w-full">

        <!-- Header: Botão Adicionar, Pesquisa e Select Matéria -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5 mb-8">

            <!-- Botão: Adicionar Assunto -->
            <x-button id="btnOpenAddAssuntoModal">
                Adicionar Assunto
                <div class="bg-[#F0E6FA] rounded-full p-1 flex items-center justify-center">
                    <x-icons.plus class="text-[#BEA2E0]" />
                </div>
            </x-button>

            <!-- Input de Pesquisa -->
            <x-search-input />

            <!-- Select da Matéria Atual -->
            <div class="relative w-full sm:w-auto min-w-50">
                <select
                    class="w-full appearance-none bg-[#BEA2E0] text-[#1F1F1F] px-5 py-2.5 rounded-full outline-none focus:ring-2 focus:ring-[#9E82C0] font-semibold text-[18px] pr-12 cursor-pointer">
                    <option value="geologia" selected>Geologia</option>
                    <option value="mineracao">Mineração</option>
                    <option value="biologia">Biologia</option>
                </select>
                <div
                    class="absolute right-3 top-1/2 -translate-y-1/2 bg-[#F0E6FA] rounded-full p-1 flex items-center justify-center pointer-events-none">

                    <x-icons.chevron-down class="text-[#BEA2E0]" />
                </div>
            </div>
        </div>

        <!-- Grid de Cards dos Assuntos -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <x-assunto-card nome="Escavação II" />
            <x-assunto-card nome="Criação de Paleotocas" />
            <x-assunto-card nome="Reviramento de Solo" />
        </div>
    </div>

    <!-- Modal Adicionar Assunto -->
    <x-modals.adicionar-assunto />

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('modalAdicionarAssunto');
            const toggleModal = () => modal.classList.toggle('hidden');

            document.getElementById('btnOpenAddAssuntoModal')?.addEventListener('click', toggleModal);
            document.getElementById('btnCancelarAssunto')?.addEventListener('click', toggleModal);
            modal.addEventListener('click', (e) => {
                if (e.target === modal) toggleModal();
            });
        });
    </script>
@endsection
