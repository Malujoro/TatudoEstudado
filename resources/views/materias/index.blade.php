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
        (function() {
            const modal = document.getElementById('modalAdicionarMateria');
            if (!modal) return; // Evita erros caso o modal não exista no DOM

            const btnAdicionar = document.getElementById('btnOpenAddModal');
            const btnCancelar = document.getElementById('btnCancelarMateria');
            const form = document.getElementById('formAdicionarMateria');
            const searchInput = document.querySelector('.mb-8 input') || document.querySelector(
                'input[type="search"]');
            const modalTitle = modal.querySelector('h2');

            let currentMateriaId = null;

            const openModal = (id = null, nome = '') => {
                currentMateriaId = id;
                modalTitle.innerText = id ? 'Editar Matéria' : 'Adicionar Matéria';
                form.elements['nome'].value = nome;
                modal.classList.remove('hidden');
            };

            const closeModal = () => modal.classList.add('hidden');

            if (btnAdicionar) btnAdicionar.addEventListener('click', () => openModal());
            if (btnCancelar) btnCancelar.addEventListener('click', closeModal);

            // Fechar ao clicar fora da caixa principal do modal
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
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

            // 2 & 3. Salvar Matéria (Adição e Edição via API)
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(form);
                const isEditing = !!currentMateriaId;
                const url = isEditing ? `/api/materias/${currentMateriaId}` : '/api/materias';
                const method = isEditing ? 'PUT' : 'POST';

                try {
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                ?.getAttribute('content')
                        },
                        body: JSON.stringify(Object.fromEntries(formData))
                    });
                    if (response.ok) {
                        closeModal();
                        typeof Turbo !== 'undefined' ?
                            Turbo.visit(window.location.href, {
                                action: "replace"
                            }) :
                            window.location.reload();
                    } else {
                        const errorData = await response.json();
                        console.error("Erro retornado pela API:", errorData);
                        alert("Erro ao adicionar a matéria. Verifique o console do navegador.");
                    }
                } catch (error) {
                    console.error("Erro ao adicionar:", error);
                }
            });

            // Abrir edição
            document.querySelectorAll('.btn-edit-materia').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const wrapper = e.target.closest('.materia-card-wrapper');
                    const id = wrapper.dataset.id;
                    const currentName = wrapper.querySelector('h2').innerText;
                    openModal(id, currentName);
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
                        if (response.ok) typeof Turbo !== 'undefined' ? Turbo.visit(window
                            .location.href, {
                                action: "replace"
                            }) : window.location.reload();
                    } catch (error) {
                        console.error("Erro ao excluir:", error);
                    }
                });
            });
        })();
    </script>
@endsection
