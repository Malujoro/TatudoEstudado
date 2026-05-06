<div id="modalAdicionarAssunto"
    class="fixed inset-0 z-50 flex items-center justify-center bg-main-dark/40 backdrop-blur-sm hidden transition-opacity p-4">
    <div class="bg-secondary-green w-full max-w-md rounded-[20px] p-8 shadow-lg relative">
        <h2 class="text-[24px] font-bold text-main-dark mb-6">Adicionar Assunto</h2>

        <form id="formAdicionarAssunto" class="flex flex-col gap-6">
            <input type="text" placeholder="Nome do Assunto" required
                class="w-full rounded-full border-none bg-white px-6 py-4 font-semibold text-purple placeholder-purple/70 outline-none focus:ring-2 focus:ring-main-dark text-lg shadow-inner" />

            <div class="flex justify-end gap-6 text-[18px] font-bold text-main-dark mt-2">
                <button type="button" id="btnCancelarAssunto"
                    class="hover:opacity-70 transition-opacity">Cancelar</button>
                <button type="submit" class="hover:opacity-70 transition-opacity">Confirmar</button>
            </div>
        </form>
    </div>
</div>
