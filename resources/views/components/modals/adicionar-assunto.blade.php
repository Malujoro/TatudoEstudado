<div id="modalAdicionarAssunto"
    class="fixed inset-0 z-50 flex items-center justify-center bg-main-dark/40 backdrop-blur-sm hidden transition-opacity p-4">
    <div class="bg-secondary-green w-full max-w-md rounded-[20px] p-8 shadow-lg relative">
        <h2 class="text-[24px] font-bold text-main-dark mb-6">Adicionar Assunto</h2>

        <form id="formAdicionarAssunto" class="flex flex-col gap-6">
            <input type="text" name="nome" placeholder="Nome do Assunto" required
                class="w-full rounded-full border-none bg-white px-6 py-4 font-semibold text-purple placeholder-purple/70 outline-none focus:ring-2 focus:ring-main-dark text-lg shadow-inner" />

            <div class="flex flex-col gap-3">
                <p class="text-sm font-semibold text-main-dark">Selecione um ou mais tipos:</p>
                <div class="flex flex-col gap-2">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="tipo[]" value="teoria" data-assunto-tipo
                            class="h-5 w-5 rounded border-purple-dim/50 text-purple focus:ring-purple" />
                        <span class="text-sm font-semibold text-main-dark">Teoria</span>
                    </label>
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="tipo[]" value="exercicio" data-assunto-tipo
                            class="h-5 w-5 rounded border-purple-dim/50 text-purple focus:ring-purple" />
                        <span class="text-sm font-semibold text-main-dark">Exercício</span>
                    </label>
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="tipo[]" value="revisao" data-assunto-tipo
                            class="h-5 w-5 rounded border-purple-dim/50 text-purple focus:ring-purple" />
                        <span class="text-sm font-semibold text-main-dark">Revisão</span>
                    </label>
                </div>
            </div>

            <!-- Matéria Vinculada de forma Oculta (preenchido automaticamente via JS) -->
            <input type="hidden" name="materia_id" id="hiddenMateriaId" />

            <div class="flex justify-end gap-6 text-[18px] font-bold text-main-dark mt-2">
                <button type="button" id="btnCancelarAssunto"
                    class="hover:opacity-70 transition-opacity">Cancelar</button>
                <button type="submit" class="hover:opacity-70 transition-opacity">Confirmar</button>
            </div>
        </form>
    </div>
</div>
