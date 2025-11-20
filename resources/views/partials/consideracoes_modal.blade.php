<div
    id="consideracoes-modal"
    class="fixed inset-0 z-50 hidden px-4 py-8"
    aria-hidden="true"
>
    <div class="absolute inset-0 bg-black bg-opacity-50" data-close-modal></div>

    <div class="relative z-10 flex h-full w-full items-center justify-center">
        <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Considerações para o cliente</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" data-close-modal aria-label="Fechar modal">
                    &times;
                </button>
            </div>

            <p class="mt-2 text-sm text-gray-600">Escreva considerações que deseja enviar para o cliente.</p>

            <div class="mt-4">
                <label for="consideracoes-textarea" class="sr-only">Considerações</label>
                <textarea
                    id="consideracoes-textarea"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    rows="4"
                    placeholder="Digite aqui as observações que deseja incluir (opcional)"
                ></textarea>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700" data-close-modal>
                    Cancelar
                </button>
                <button
                    type="button"
                    id="consideracoes-confirm"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                >
                    Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('consideracoes-modal');
            const textarea = document.getElementById('consideracoes-textarea');
            const confirmButton = document.getElementById('consideracoes-confirm');
            const closeButtons = modal?.querySelectorAll('[data-close-modal]') ?? [];
            const openButtons = document.querySelectorAll('[data-open-consideracoes]');
            let targetForm = null;

            if (! modal) {
                return;
            }

            function openModal(button) {
                const formId = button.getAttribute('data-target-form');
                targetForm = formId ? document.getElementById(formId) : null;

                if (! targetForm) {
                    return;
                }

                textarea.value = '';
                modal.classList.remove('hidden');
                modal.setAttribute('aria-hidden', 'false');
                textarea.focus();
            }

            function closeModal() {
                modal.classList.add('hidden');
                modal.setAttribute('aria-hidden', 'true');
                textarea.value = '';
                targetForm = null;
            }

            openButtons.forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    openModal(button);
                });
            });

            confirmButton?.addEventListener('click', () => {
                if (! targetForm) {
                    return;
                }

                const consideracoesText = textarea.value.trim();

                if (targetForm.dataset.exportClientSide === 'true') {
                    const exportEvent = new CustomEvent('consideracoes:export', {
                        detail: {
                            form: targetForm,
                            text: consideracoesText,
                        },
                    });

                    document.dispatchEvent(exportEvent);
                    closeModal();
                    return;
                }

                const hiddenInput = targetForm.querySelector('input[name="consideracoes"]');

                if (hiddenInput) {
                    hiddenInput.value = consideracoesText;
                }

                targetForm.submit();
                closeModal();
            });

            closeButtons.forEach((button) => {
                button.addEventListener('click', closeModal);
            });
        });
    </script>
@endpush
