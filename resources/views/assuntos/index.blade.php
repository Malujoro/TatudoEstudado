@extends('layouts.sidebar')

@section('content')
    <div class="flex flex-col h-full w-full pt-8">

        <!-- Header: Select Matéria e Barra de Pesquisa -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
            <div class="md:col-start-1 xl:col-start-2">
                <!-- Select da Matéria Atual -->
                <div class="relative w-full">
                    @php
                        $selectedMateriaId = request('materia_id');
                    @endphp
                    <select id="materiaSelect"
                        class="w-full appearance-none bg-purple-light text-main-dark px-5 py-2.5 rounded-full outline-none focus:ring-2 focus:ring-purple font-semibold text-[18px] pr-12 cursor-pointer">
                        @foreach ($materias as $materia)
                            <option value="{{ $materia->id }}" @selected($materia->id == $selectedMateriaId)>{{ $materia->nome }}</option>
                        @endforeach
                    </select>
                    <div
                        class="absolute right-3 top-1/2 -translate-y-1/2 bg-purple-lightest rounded-full p-1 flex items-center justify-center pointer-events-none">
                        <x-icons.chevron-down class="text-purple-light" />
                    </div>
                </div>
            </div>
            <div class="md:col-start-2 xl:col-start-3">
                <!-- Input de Pesquisa -->
                <x-search-input class="py-3" container-class="w-full" />
            </div>
        </div>

        <!-- Grid de Cards dos Assuntos -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

            <!-- Card: Adicionar Assunto -->
            <button id="btnOpenAddAssuntoModal"
                class="bg-secondary-green rounded-[20px] p-6 flex flex-col items-center justify-center min-h-42.5 text-main-dark hover:bg-secondary-green/90 transition-colors shadow-sm border-2 border-dashed border-main-dark/10">
                <div class="bg-purple-lightest rounded-full p-3 mb-3 flex items-center justify-center">
                    <x-icons.plus class="w-10 h-10 text-purple-light" />
                </div>
                <span class="text-[16px] font-bold tracking-wide opacity-80">Adicionar assunto</span>
            </button>

            @foreach ($assuntos as $assunto)
                <div class="assunto-card-wrapper" data-nome="{{ strtolower($assunto->nome) }}"
                    data-nome-real="{{ $assunto->nome }}" data-materia-id="{{ $assunto->materia_id }}"
                    data-id="{{ $assunto->id }}" data-tipo='@json($assunto->tipo)'
                    data-caderno-id="{{ $assunto->caderno->id ?? '' }}"
                    data-caderno-conteudo="{{ $assunto->caderno->conteudo ?? '' }}">
                    <x-assunto-card :nome="$assunto->nome" :id="$assunto->id" :tipo="$assunto->tipo" />
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal Caderno de Erros -->
    <div class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-3" data-modal-caderno>
        <div class="flex h-[94vh] w-full max-w-6xl flex-col rounded-3xl bg-white p-6">
            <div class="flex items-center justify-between">
                <h3 class="font-rem text-lg font-semibold text-purple-night">Caderno de erros</h3>
                <button type="button" class="text-purple-night" data-close-modal-caderno>✕</button>
            </div>
            <textarea
                class="mt-4 min-h-0 flex-1 w-full rounded-2xl border border-purple-dim/50 bg-white px-4 py-3 text-sm text-purple-night"
                data-caderno-texto></textarea>
            <div class="mt-4 flex justify-end gap-2">
                <button type="button"
                    class="rounded-full border border-purple-dim px-4 py-2 text-xs font-semibold text-purple-dim hover:bg-purple-dim hover:text-white transition"
                    data-close-modal-caderno>Cancelar</button>
                <button type="button"
                    class="rounded-full bg-purple-light px-4 py-2 text-xs font-semibold text-purple-night hover:opacity-80 transition"
                    data-save-caderno>Salvar</button>
            </div>
        </div>
    </div>

    <!-- Modal Adicionar Assunto -->
    <x-modals.adicionar-assunto />

    <script>
        (function() {
            const modal = document.getElementById('modalAdicionarAssunto');
            if (!modal) return; // Evita erros se o modal não existir na visualização

            const form = document.getElementById('formAdicionarAssunto');
            const searchInput = document.querySelector('.mb-8 input') || document.querySelector(
                'input[type="search"]');
            const materiaSelect = document.getElementById('materiaSelect');
            const btnAdicionar = document.getElementById('btnOpenAddAssuntoModal');
            const btnCancelar = document.getElementById('btnCancelarAssunto');
            const modalTitle = modal.querySelector('h2');
            const tipoCheckboxes = Array.from(modal.querySelectorAll('[data-assunto-tipo]'));
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            let currentAssuntoId = null;

            const setTipos = (tipos) => {
                const normalized = Array.isArray(tipos) ? tipos : [];

                tipoCheckboxes.forEach(cb => {
                    cb.checked = normalized.includes(cb.value);
                });
            };

            const getTiposSelecionados = () => {
                return tipoCheckboxes.filter(cb => cb.checked).map(cb => cb.value);
            };

            const openModal = (id = null, nome = '', materiaId = null, tipo = null) => {
                currentAssuntoId = id;
                if (id) {
                    modalTitle.innerText = 'Editar Assunto';
                    form.elements['nome'].value = nome;
                    document.getElementById('hiddenMateriaId').value = materiaId;
                    setTipos(tipo);
                } else {
                    modalTitle.innerText = 'Adicionar Assunto';
                    form.elements['nome'].value = '';
                    if (materiaSelect) {
                        document.getElementById('hiddenMateriaId').value = materiaSelect.value;
                    }
                    setTipos([]);
                }
                modal.classList.remove('hidden');
            };

            const closeModal = () => modal.classList.add('hidden');

            if (btnAdicionar) btnAdicionar.addEventListener('click', () => openModal());
            if (btnCancelar) btnCancelar.addEventListener('click', closeModal);

            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });

            // --- Funções Auxiliares de API e UI ---
            function atualizarTela() {
                typeof Turbo !== 'undefined' ? Turbo.visit(window.location.href, {
                    action: "replace"
                }) : window.location.reload();
            }

            async function checarEGerarCronograma(titulo) {
                if (typeof window.promptGerarCronograma === 'function') {
                    await window.promptGerarCronograma(titulo);
                } else {
                    const result = await Swal.fire({
                        title: titulo,
                        text: 'Deseja gerar um novo cronograma para aplicar as mudanças?',
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonColor: 'var(--color-swal-confirm)',
                        cancelButtonColor: 'var(--color-swal-cancel)',
                        confirmButtonText: 'Gerar cronograma',
                        cancelButtonText: 'Manter atual'
                    });

                    if (!result.isConfirmed) return;

                    try {
                        await fetch('/api/cronograma/gerar', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            }
                        });
                    } catch (error) {
                        console.error("Erro ao gerar cronograma:", error);
                    }
                }
            }

            async function salvarAssunto(url, method, payload) {
                try {
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify(payload)
                    });
                    if (response.ok) {
                        closeModal();
                        
                        await new Promise(resolve => setTimeout(resolve, 100));
                        
                        await checarEGerarCronograma('Assunto salvo com sucesso!');
                        atualizarTela();
                    }
                } catch (error) {
                    console.error("Erro ao salvar:", error);
                }
            }

            async function excluirAssunto(id) {
                try {
                    const response = await fetch(`/api/assuntos/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        }
                    });
                    if (response.ok) {
                        await checarEGerarCronograma('Assunto excluído!');
                        atualizarTela();
                    }
                } catch (error) {
                    console.error("Erro ao excluir:", error);
                }
            }

            // 1. Filtro Local Integrado (Pesquisa + Dropdown de Matéria)
            function filterAssuntos() {
                const term = searchInput ? searchInput.value.toLowerCase() : '';
                const materiaId = materiaSelect ? materiaSelect.value : '';

                document.querySelectorAll('.assunto-card-wrapper').forEach(card => {
                    const matchesNome = (card.dataset.nome || '').includes(term);
                    const matchesMateria = !materiaId || card.dataset.materiaId === materiaId;

                    card.style.display = (matchesNome && matchesMateria) ? 'block' : 'none';
                });
            }
            if (searchInput) searchInput.addEventListener('input', filterAssuntos);
            
            if (materiaSelect) {
                materiaSelect.addEventListener('change', () => {
                    filterAssuntos();
                    
                    try {
                        const url = new URL(window.location.href);
                        if (materiaSelect.value) {
                            url.searchParams.set('materia_id', materiaSelect.value);
                        } else {
                            url.searchParams.delete('materia_id');
                        }
                        window.history.replaceState({}, '', url.toString());
                    } catch (error) {
                        console.error('Erro ao atualizar URL:', error);
                    }
                });
            }

            filterAssuntos(); // Oculta itens de outras matérias que não sejam a padrão na hora que carrega a tela

            // 2 & 3. Salvar Assunto (Adição e Edição via API)
            if (form) {
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const isEditing = !!currentAssuntoId;
                    const url = isEditing ? `/api/assuntos/${currentAssuntoId}` : '/api/assuntos';
                    const method = isEditing ? 'PUT' : 'POST';

                    const tipos = getTiposSelecionados();
                    if (tipos.length < 1) {
                        Swal.fire({
                            title: 'Atenção!',
                            text: 'Selecione pelo menos 1 tipo (Teoria, Exercício ou Revisão).',
                            icon: 'warning',
                            confirmButtonColor: '#9333EA'
                        });
                        return;
                    }

                    const payload = {
                        nome: form.elements['nome']?.value,
                        materia_id: document.getElementById('hiddenMateriaId')?.value,
                        tipo: tipos,
                    };

                    await salvarAssunto(url, method, payload);
                });
            }

            // Abrir edição
            document.querySelectorAll('.btn-edit-assunto').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const wrapper = e.target.closest('.assunto-card-wrapper');
                    const id = wrapper.dataset.id;
                    const materiaId = wrapper.dataset.materiaId;
                    const rawTipo = wrapper.dataset.tipo;
                    let tipo = null;
                    try {
                        tipo = rawTipo ? JSON.parse(rawTipo) : null;
                    } catch {
                        tipo = rawTipo || null;
                    }
                    const currentName = wrapper.dataset.nomeReal;
                    openModal(id, currentName, materiaId, tipo);
                });
            });

            // Clicar no card executa a edição por padrão (exceto nos botões)
            document.querySelectorAll('.assunto-card-wrapper').forEach(wrapper => {
                wrapper.style.cursor = 'pointer';
                wrapper.addEventListener('click', (e) => {
                    if (e.target.closest('.action-buttons')) return;
                    wrapper.querySelector('.btn-edit-assunto')?.click();
                });
            });

            // 4. Deletar Assunto
            document.querySelectorAll('.btn-delete-assunto').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    e.stopPropagation();
                    const result = await Swal.fire({
                        title: 'Atenção!',
                        text: 'Tem certeza que deseja excluir este assunto?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: 'var(--color-swal-delete)',
                        cancelButtonColor: 'var(--color-swal-cancel)',
                        confirmButtonText: 'Sim, excluir',
                        cancelButtonText: 'Cancelar'
                    });

                    if (result.isConfirmed) {
                        const id = e.target.closest('.assunto-card-wrapper').dataset.id;
                        await excluirAssunto(id);
                    }
                });
            });

            // 5. Caderno de erros
            const modalCaderno = document.querySelector('[data-modal-caderno]');
            const textoCaderno = document.querySelector('[data-caderno-texto]');
            const saveCaderno = document.querySelector('[data-save-caderno]');
            const closeCaderno = document.querySelectorAll('[data-close-modal-caderno]');
            let cadernoState = {
                assuntoId: null,
                cadernoId: null
            };

            document.querySelectorAll('.btn-caderno-assunto').forEach((button) => {
                button.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const wrapper = e.target.closest('.assunto-card-wrapper');
                    cadernoState = {
                        assuntoId: wrapper.dataset.id,
                        cadernoId: wrapper.dataset.cadernoId || null,
                    };
                    if (textoCaderno) textoCaderno.value = wrapper.dataset.cadernoConteudo || '';
                    if (modalCaderno) {
                        modalCaderno.classList.remove('hidden');
                        modalCaderno.classList.add('flex');
                    }
                });
            });

            closeCaderno.forEach((button) => {
                button.addEventListener('click', () => {
                    modalCaderno?.classList.add('hidden');
                    modalCaderno?.classList.remove('flex');
                });
            });

            saveCaderno?.addEventListener('click', async () => {
                if (!cadernoState.assuntoId) return;
                const conteudo = textoCaderno?.value || '';
                const isUpdate = Boolean(cadernoState.cadernoId);
                const url = isUpdate ? `/api/cadernos/${cadernoState.cadernoId}` : '/api/cadernos';

                try {
                    const response = await fetch(url, {
                        method: isUpdate ? 'PUT' : 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(isUpdate ? {
                            conteudo
                        } : {
                            conteudo,
                            assunto_id: cadernoState.assuntoId
                        }),
                    });
                    if (response.ok) {
                        modalCaderno?.classList.add('hidden');
                        modalCaderno?.classList.remove('flex');
                        atualizarTela();
                    }
                } catch (error) {
                    console.error("Erro ao salvar caderno:", error);
                }
            });
        })();
    </script>
@endsection
