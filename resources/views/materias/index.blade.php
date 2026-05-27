@extends('layouts.sidebar')

@section('content')
    <div class="flex flex-col h-full w-full">

        <!-- Header: Botão Adicionar e Barra de Pesquisa -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
            <div class="md:col-start-2 xl:col-start-3">
                <!-- Input de Pesquisa -->
                <x-search-input class="py-3" container-class="w-full" />
            </div>
        </div>

        <!-- Grid de Cards das Matérias -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

            <!-- Card: Adicionar Matéria -->
            <button id="btnOpenAddModal"
                class="bg-purple rounded-[20px] p-6 flex flex-col items-center justify-center min-h-42.5 text-main-dark hover:bg-purple/90 transition-colors shadow-sm border-2 border-dashed border-main-dark/10">
                <div class="bg-purple-lightest rounded-full p-3 mb-3 flex items-center justify-center">
                    <x-icons.plus class="w-10 h-10 text-purple-light" />
                </div>
                <span class="text-[16px] font-bold tracking-wide opacity-80">Adicionar matéria</span>
            </button>

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
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

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

            // --- Funções Auxiliares de API e UI ---
            function atualizarTela() {
                typeof Turbo !== 'undefined' ? Turbo.visit(window.location.href, {
                    action: "replace"
                }) : window.location.reload();
            }

            async function excluirMateria(id) {
                try {
                    const response = await fetch(`/api/materias/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        }
                    });
                    if (response.ok) {
                        await Swal.fire({
                            title: 'Sucesso!',
                            text: 'Matéria excluída com sucesso.',
                            icon: 'success',
                            confirmButtonColor: 'var(--color-swal-confirm)'
                        });
                        atualizarTela();
                    }
                } catch (error) {
                    console.error("Erro ao excluir:", error);
                }
            }

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
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify(Object.fromEntries(formData))
                    });
                    if (response.ok) {
                        closeModal();
                        
                        await new Promise(resolve => setTimeout(resolve, 100));
                        
                        await Swal.fire({
                            title: 'Sucesso!',
                            text: 'Matéria salva com sucesso.',
                            icon: 'success',
                            confirmButtonColor: 'var(--color-swal-confirm)'
                        });
                        atualizarTela();
                    } else {
                        const errorData = await response.json();
                        Swal.fire({
                            title: 'Erro!',
                            text: 'Erro ao adicionar a matéria. Verifique o console do navegador.',
                            icon: 'error',
                            confirmButtonColor: 'var(--color-swal-confirm)'
                        });
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
                    e.stopPropagation();
                    const result = await Swal.fire({
                        title: 'Atenção!',
                        text: 'Tem certeza que deseja excluir esta matéria? Todos os assuntos vinculados a ela serão excluídos.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: 'var(--color-swal-delete)',
                        cancelButtonColor: 'var(--color-swal-cancel)',
                        confirmButtonText: 'Sim, excluir',
                        cancelButtonText: 'Cancelar'
                    });

                    if (result.isConfirmed) {
                        const id = e.target.closest('.materia-card-wrapper').dataset.id;
                        await excluirMateria(id);
                    }
                });
            });
        })();
    </script>
@endsection
