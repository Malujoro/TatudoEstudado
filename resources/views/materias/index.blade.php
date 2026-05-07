@extends('layouts.sidebar')

@section('content')
    <div class="flex flex-col h-full w-full">

        <!-- Header: Botão Adicionar e Barra de Pesquisa -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5 mb-8">

            <!-- Botão: Adicionar Matéria -->
            <x-button id="btnOpenAddModal">
                Adicionar Matéria
                <div class="bg-purple-lightest rounded-full p-1 flex items-center justify-center">
                    <x-icons.plus class="text-purple-light" />
                </div>
            </x-button>

            <!-- Input de Pesquisa -->
            <x-search-input class="py-3" />
        </div>

        <!-- Grid de Cards das Matérias -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach ($materias as $materia)
                <div class="materia-card-wrapper" data-nome="{{ strtolower($materia->nome) }}" data-id="{{ $materia->id }}">
                    <x-materia-card :nome="$materia->nome" :id="$materia->id" :quantidade="$materia->assuntos_count" />
                </div>
            @endforeach
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
            const searchInput = document.querySelector('.mb-8 input') || document.querySelector(
                'input[type="search"]');

            const toggleModal = () => modal.classList.toggle('hidden');

            if (btnAdicionar) btnAdicionar.addEventListener('click', toggleModal);
            if (btnCancelar) btnCancelar.addEventListener('click', toggleModal);

            // Fechar ao clicar fora da caixa principal do modal
            modal.addEventListener('click', (e) => {
                if (e.target === modal) toggleModal();
            });

            // 1. Pesquisa Local
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    const term = e.target.value.toLowerCase();
                    document.querySelectorAll('.materia-card-wrapper').forEach(card => {
                        const nome = card.dataset.nome || '';
                        card.style.display = nome.includes(term) ? 'block' : 'none';
                    });
                });
            }

            // 2. Adicionar Matéria consumindo a API
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(form);
                try {
                    const response = await fetch('/api/materias', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                ?.getAttribute('content')
                        },
                        body: JSON.stringify(Object.fromEntries(formData))
                    });
                    if (response.ok) {
                        window.location
                            .reload(); // Recarrega a página para atualizar a listagem com a nova matéria
                    } else {
                        const errorData = await response.json();
                        console.error("Erro retornado pela API:", errorData);
                        alert("Erro ao adicionar a matéria. Verifique o console do navegador.");
                    }
                } catch (error) {
                    console.error("Erro ao adicionar:", error);
                }
            });

            // 3. Editar Matéria
            document.querySelectorAll('.btn-edit-materia').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    const wrapper = e.target.closest('.materia-card-wrapper');
                    const id = wrapper.dataset.id;
                    const currentName = wrapper.querySelector('h2').innerText;
                    const novoNome = prompt("Editar nome da matéria:", currentName);
                    if (novoNome && novoNome.trim() !== "" && novoNome !== currentName) {
                        try {
                            const response = await fetch(`/api/materias/${id}`, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]')?.getAttribute(
                                        'content')
                                },
                                body: JSON.stringify({
                                    nome: novoNome
                                })
                            });
                            if (response.ok) window.location.reload();
                        } catch (error) {
                            console.error("Erro ao editar:", error);
                        }
                    }
                });
            });

            // 4. Deletar Matéria
            document.querySelectorAll('.btn-delete-materia').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    if (!confirm(
                            "Tem certeza que deseja excluir esta matéria? Todos os assuntos vinculados a ela serão excluídos."
                        )) return;
                    const id = e.target.closest('.materia-card-wrapper').dataset.id;
                    try {
                        const response = await fetch(`/api/materias/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]')?.getAttribute(
                                    'content')
                            }
                        });
                        if (response.ok) window.location.reload();
                    } catch (error) {
                        console.error("Erro ao excluir:", error);
                    }
                });
            });
        });
    </script>
@endsection
