function callAPI() {
    fetch(`${reservasAjax.ajax_url}?action=reservas_call_api&nonce=${reservasAjax.nonce}`)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                console.log("📦 Dados da API Hostkit:");
                console.log(result.data);
            } else {
                console.error("❌ Erro da API:", result.data.message);
            }
        })
        .catch(error => {
            console.error("❌ Erro AJAX:", error);
        });
}
