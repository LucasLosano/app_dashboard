$(document).ready(() => {
	$("#documentacao").on('click',()=>{
        $.post("documentacao.html",data =>{
            $("#pagina").html(data);
        })
    })
    $("#suporte").on('click',()=>{
        $.get("suporte.html",data =>{
            $("#pagina").html(data);
        })
    })
    $("#dashboard").on('click',()=>{
        location.reload();
    })
    $("#competencia").on('change',e =>{
        
        let competencia = $(e.target).val()

        $.ajax({
            type: 'GET',
            url: 'app.php',
            data: 'acao=competencia&competencia=' + competencia,
            dataType: 'json',
            success: dados => {
                console.log(dados)
                $('#numeroVendas').html(dados.num_vendas)
                $('#totalVendas').html(dados.num_total)
                $('#despesas').html(dados.despesas)
            },
            error : erro => {console.log(erro)}
        })
    })


    $.ajax({
        type: 'GET',
        url: 'app.php',
        data: 'acao=cliente',
        dataType: 'json',
        success: dados => {
            $('#clientesAtivos').html(dados.clientes_ativos)
            $('#clientesInativos').html(dados.clientes_inativos)
            $('#elogios').html(dados.elogios)
            $('#sugestoes').html(dados.sugestoes)
            $('#reclamacoes').html(dados.reclamacoes)
        },
        erro: erro =>{console.log(erro);}

    })
})




