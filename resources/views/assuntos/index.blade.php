@extends('layouts.sidebar')

@section('content')
    <div class="flex flex-col h-full w-full">

        <!-- Header: Botão Adicionar, Pesquisa e Select Matéria -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5 mb-8">

            <!-- Botão: Adicionar Assunto -->
            <x-button id="btnOpenAddAssuntoModal">
                Adicionar Assunto
                <div class="bg-purple-lightest rounded-full p-1 flex items-center justify-center">
                    <x-icons.plus class="text-purple-light" />
                </div>
            </x-button>

            <!-- Input de Pesquisa -->
            <x-search-input />

            <!-- Select da Matéria Atual -->
            <div class="relative w-full sm:w-auto min-w-50">
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

        <!-- Grid de Cards dos Assuntos -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach ($assuntos as $assunto)
                <div class="assunto-card-wrapper" data-nome="{{ strtolower($assunto->nome) }}"
                    data-materia-id="{{ $assunto->materia_id }}" data-id="{{ $assunto->id }}" data-tipo='@json($assunto->tipo)'>
                    <x-assunto-card :nome="$assunto->nome" :id="$assunto->id" :tipo="$assunto->tipo" />
                </div>
            @endforeach
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
            if (materiaSelect) materiaSelect.addEventListener('change', filterAssuntos);

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
                        alert('Selecione pelo menos 1 tipo (Teoria, Exercício ou Revisão).');
                        return;
                    }

                    const payload = {
                        nome: form.elements['nome']?.value,
                        materia_id: document.getElementById('hiddenMateriaId')?.value,
                        tipo: tipos,
                    };

                    try {
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]')?.getAttribute('content')
                            },
                            body: JSON.stringify(payload)
                        });
                        if (response.ok) {
                            closeModal();
                            if (confirm("Assunto salvo com sucesso! Deseja gerar um novo cronograma para aplicar as mudanças?")) {
                                await fetch('/api/cronograma/gerar', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                                        'Accept': 'application/json'
                                    }
                                });
                            }
                            typeof Turbo !== 'undefined' ? Turbo.visit(window.location.href, {
                                action: "replace"
                            }) : window.location.reload();
                        }
                    } catch (error) {
                        console.error("Erro ao salvar:", error);
                    }
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
                    const currentName = wrapper.querySelector('h2').innerText;
                    openModal(id, currentName, materiaId, tipo);
                });
            });

            // 4. Deletar Assunto
            document.querySelectorAll('.btn-delete-assunto').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    if (!confirm("Tem certeza que deseja excluir este assunto?")) return;
                    const id = e.target.closest('.assunto-card-wrapper').dataset.id;
                    try {
                        const response = await fetch(`/api/assuntos/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]')?.getAttribute(
                                    'content')
                            }
                        });
                        if (response.ok) {
                            if (confirm("Assunto excluído! Deseja gerar um novo cronograma para aplicar as mudanças?")) {
                                await fetch('/api/cronograma/gerar', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                                        'Accept': 'application/json'
                                    }
                                });
                            }
                            typeof Turbo !== 'undefined' ? Turbo.visit(window.location.href, {
                                action: "replace"
                            }) : window.location.reload();
                        }
                    } catch (error) {
                        console.error("Erro ao excluir:", error);
                    }
                });
            });
        })();
    </script>
@endsection
