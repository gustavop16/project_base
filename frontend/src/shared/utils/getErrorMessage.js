export function getErrorMessage(errorResponse) {
  try {
    const errors = errorResponse?.data?.errors;

    if (errors && typeof errors === 'object') {
      const allMessages = [];

      for (const key in errors) {
        if (Array.isArray(errors[key])) {
          allMessages.push(...errors[key]);
        }
      }

      return allMessages.join(' | ');
    }

    // Caso contrário, usa a mensagem única
    return errorResponse?.data?.message || 'Erro desconhecido.';
  } catch (e) {
    return 'Erro ao processar a mensagem de erro.';
  }
}
