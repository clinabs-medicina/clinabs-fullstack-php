function certificado() {
    Swal.fire({
        title: "Selecione Seu Certificado Digital",
        html: `
                <div class="certificadores">
                    <div class="form-div">
                        <a data-href="#" onclick="cert(this)" data-id="certisign"><img src="/assets/images/certificadores/certisign.png" height="48px"></a>
                    </div>

                    <div class="form-div">
                        <a data-href="https://painel.birdid.com.br/#/enrollment-1/portal" onclick="cert(this)" data-id="birdid" target="_birdID"><img src="/assets/images/certificadores/logo-bird-id.webp" height="48px"></a>
                    </div>

                    <div class="form-div">
                        <a data-href="https://assinar-com.vidaas.com.br/" onclick="cert(this)" data-id="vidas" target="_vidas"><img src="/assets/images/certificadores/logo-vidaas.png" height="48px"></a>
                    </div>
                </div>
            `,
        showConfirmButton: false,
        showCancelButton: false,
        cancelButtonText: "FECHAR",
        width: "auto",
        height: "256px"
    }
    );
}

function cert(elem) {
    let token = $("#tablePrescricao").data("token");
    let cid = window.prompt("Digite o CID (não obrigatório)", "");
    let cert = $(elem).data('id');
    let href = $(elem).data('href');

    Swal.close();

    if (cert === "certisign") {
        $.get(`/api/pdf/receita.php?token=${token}&cid=${cid}`);

        window.open(`/api/v2/certisign/index.php?token=${token}&cid=${cid}`, "_certisign");
    } else {
        downloadReceita(cid);
        window.open(href, $(elem).data('target'));
    }
}