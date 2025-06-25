<?php
/**
 * Plugin Name: Reservas
 * Description: Plugin de reservas embaixador
 * Author: IBloom
 * Author URI: https://www.ibloom.digital/pt/agencia-digital-lisboa/
 * Version: 0.1
 * Text Domain: Reservas
 * 
 */

 // Function to add the "Reservas" menu item
function cmp_add_reservas_menu() {
    add_menu_page(
        'Reservas',          // Page title
        'Reservas',          // Menu title
        'manage_options',   // Capability
        'cmp-reservas',      // Menu slug
        'cmp_reservas_page', // Function to display the page content
        'dashicons-awards', // Icon URL (Dashicons: https://developer.wordpress.org/resource/dashicons/)
        2                   // Position
    );
     // Add subpages
     add_submenu_page(
        'cmp-winners',          // Parent slug
        'Ver recompensas',           // Page title
        'Ver recompensas',           // Menu title
        'manage_options',       // Capability
        'cmp-ver_recompensas',       // Menu slug
        'cmp_ver_recompensas'   // Function to display the subpage content
    );
    add_submenu_page(
        'cmp-winners',          // Parent slug
        'Adicionar Progresso',           // Page title
        'Adicionar Progresso',           // Menu title
        'manage_options',       // Capability
        'cmp-adicionar_progresso',       // Menu slug
        'cmp_adicionar_progresso'   // Function to display the subpage content
    );
}

// Hook the function to 'admin_menu' action
add_action('admin_menu', 'cmp_add_winners_menu');

function buscar_dados() {
    $username = wp_get_current_user()->user_login;
    
    switch ($username) {
        case "RodrigoWDWP":
            $id = 1;
            break;
        case "JoaoWDWP":
            $id = 2;
            break;
        case "LuizWDWP":
            $id = 3;
            break;
        case "TiagoWDWP":
            $id = 4;
            break;
    }
    
    global $wpdb;
    $result = $wpdb->get_results("SELECT ProgressoInd, ProgressoCnt, ProgressoEq, MCinzentas, MDouradas FROM Winners WHERE ID = $id");

    foreach ($result as $row) {
        $dados['PI'] = $row->ProgressoInd;
        $dados['PC'] = $row->ProgressoCnt;
        $dados['PE'] = $row->ProgressoEq;
        $dados['MC'] = $row->MCinzentas;
        $dados['MD'] = $row->MDouradas;
    }
    return $dados;
}

// Function to display the "Winners" page content
function cmp_winners_page() {
    ?>
    <head>
    <style>
      .material-symbols-outlined {
        font-variation-settings:
          'FILL' 0,
          'wght' 500,
          'GRAD' 0,
          'opsz' 400
      }
      .content{
          margin-left: 0px;
          padding: 0px;
      }
      .title{
          text-align: center;
          font-size: 500%;
      }
      .obj-container{
          width: 50%;
          display: flex;
          justify-content: center; /* Horizontally center */
          align-items: center; /* Vertically center */
      }
      .cnt-container{
          width: 100%;
          display: flex;
          justify-content: center; /* Horizontally center */
          align-items: center; /* Vertically center */
      }
      
      .container{
          height: 350px;
          width: 50%;
          display: grid;
          place-items: center;
      }
      .circular-progress{
          position: relative;
          height: 250px;
          width: 250px;
          border-radius: 50%;
          display: grid;
          place-items: center;
      }
      .circular-progress:before{
          content: "";
          position: absolute;
          height: 84%;
          width: 84%;
          border-radius: 50%;
          background-color: rgb(240, 240, 241);
      }
      .value-container{
          position: relative;
          font-family: "Poppins", sans-serif;
          font-size: 50px;
          color: #231c3d;
      }
      .objetivos {
          display: flex;
          justify-content: center; /* Horizontally center */
          align-items: center; /* Vertically center */
          margin-top: 4%;
      }
      .objetivos p{
          font-family: "Poppins", sans-serif;
      }
      .objetivos:after{
          content: "";
          display: table;
          clear: both;
      }
      .descObjetivo{
        font-size: 20px;
      }
      </style>
  </head>
  <h1 class="title">Objetivos</h1>
    <div class="content" id="barras">
            <div class="objetivos">
                <div class="obj-container">
                    <div class="container">
                        <div class="circular-progress" id="barra1">
                            <div class="value-container" id="valor1">0%</div>
                        </div>
                    <p class="descObjetivo">Objetivo pessoal</p>
                    </div>
                </div>
                <div class="obj-container">
                    
                    <div class="container">
                        <div class="circular-progress" id="barra3">
                            <div class="value-container" id="valor3">0%</div>
                        </div>
                        <p class="descObjetivo">Objetivo de contactos</p>
                    </div>
                </div>
            </div>
            <div class="objetivos">
                <div class="cnt-container">
                    <div class="container">
                        <div class="circular-progress" id="barra2">
                            <div class="value-container" id="valor2">0%</div>
                        </div>
                    <p class="descObjetivo">Objetivo de equipa</p>
                    </div>
                    
                
                </div>
            </div>
        </div>
        <script>
        let progressBar1 = document.getElementById("barra1");
        let progressBar2 = document.getElementById("barra2");
        let progressBar3 = document.getElementById("barra3");
        let valueContainer1 = document.getElementById("valor1");
        let valueContainer2 = document.getElementById("valor2");
        let valueContainer3 = document.getElementById("valor3");

        <?php 
            $dados = buscar_dados(); 
        ?>

        let progressValue1 = 0;
        let progressValue2 = 0;
        let progressValue3 = 0;
        let progressEndValueInd = <?php echo $dados['PI'] ?>;
        let progressEndValueEq = <?php echo $dados['PE'] ?>;
        let progressEndValueCont = <?php echo $dados['PC'] ?>;
        let speed = 50;
        //----------------Individual----------------------------
        let progress1 = setInterval(() => {
        progressValue1++;
        valueContainer1.textContent = `${progressValue1}%`;
        progressBar1.style.background = `conic-gradient(
            #4d5bf9 ${progressValue1 * 3.6}deg,
            #cadcff ${progressValue1 * 3.6}deg
        )`;
        console.log(progressValue1);
        if (progressValue1 >= progressEndValueInd) {
            clearInterval(progress1);
        }
        }, speed);
        //--------------------Equipa----------------------------
        let progress2 = setInterval(() => {
        progressValue2++;
        valueContainer2.textContent = `${progressValue2}%`;
        progressBar2.style.background = `conic-gradient(
            #4d5bf9 ${progressValue2 * 3.6}deg,
            #cadcff ${progressValue2 * 3.6}deg
        )`;
        if (progressValue2 >= progressEndValueEq) {
            clearInterval(progress2);
        }
        }, speed);
        //---------------------Contactos------------------------
        let progress3 = setInterval(() => {
        progressValue3++;
        valueContainer3.textContent = `${progressValue3}%`;
        progressBar3.style.background = `conic-gradient(
            #4d5bf9 ${progressValue3 * 3.6}deg,
            #cadcff ${progressValue3 * 3.6}deg
        )`;
        if (progressValue3 >= progressEndValueCont) {
            clearInterval(progress3);
        }
        }, speed);
        //------------------------------------------------------
        function myFunction(){
            alert("FUNCEMINA PORRA")
        }
    </script>
    <?php
}
function cmp_ver_recompensas() {
    ?>
    <style>
        .title{
          text-align: center;
          font-size: 500%;
      }
      .description{
          text-align: center;
          font-size: 400%;
      }
    </style>
    <h1 class="title">Recompensas</h1>
    <p class="description">Aqui podes trocar as tuas moedas por recompensas pelo teu esforço</p>
    <style type="text/css">
    .tg {
        border-collapse: collapse;
        border-spacing: 0;
        margin: 3vh auto;
        width: 80vw;
    }
    .tg td {
        border-color: black;
        border-style: solid;
        border-width: 1px;
        font-family: Arial, sans-serif;
        font-size: 14px;
        overflow: hidden;
        padding: 10px 5px;
        word-break: normal;
    }
    .tg th {
        border-color: black;
        border-style: solid;
        border-width: 1px;
        font-family: Arial, sans-serif;
        font-size: 14px;
        font-weight: normal;
        overflow: hidden;
        padding: 10px 5px;
        word-break: normal;
    }
    .tg .tg-ul38 {
        position: -webkit-sticky;
        position: sticky;
        text-align: center;
        top: -1px;
        vertical-align: top;
        will-change: transform;
    }
    .tg .tg-0lax {
        text-align: center;
        vertical-align: center;
    }
    .tg-sort-header::-moz-selection {
        background: 0 0;
    }
    .tg-sort-header::selection {
        background: 0 0;
    }
    .tg-sort-header {
        cursor: pointer;
    }
    .tg-sort-header:after {
        content: "";
        float: right;
        margin-top: 7px;
        border-width: 0 5px 5px;
        border-style: solid;
        border-color: #404040 transparent;
        visibility: hidden;
    }
    .tg-sort-header:hover:after {
        visibility: visible;
    }
    .tg-sort-asc:after,
    .tg-sort-asc:hover:after,
    .tg-sort-desc:after {
        visibility: visible;
        opacity: 0.4;
    }
    .tg-sort-desc:after {
        border-bottom: none;
        border-width: 5px 5px 0;
    }
</style>
<table id="tg-WItVm" class="tg">
    <thead>
        <tr>
            <th class="tg-ul38">Nome</th>
            <th class="tg-ul38">Descrição</th>
            <th class="tg-ul38">Preço</th>
            <th class="tg-ul38">Comprar</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="tg-0lax">Batata</td>
            <td class="tg-0lax">Uma batata</td>
            <td class="tg-0lax">1000</td>
            <td class="tg-0lax">Botão</td>
        </tr>
        <tr>
            <td class="tg-0lax">Qualquer coisa</td>
            <td class="tg-0lax">Cena qualquer</td>
            <td class="tg-0lax">1500</td>
            <td class="tg-0lax">Botão</td>
        </tr>
        <tr>
            <td class="tg-0lax">Afonos</td>
            <td class="tg-0lax">Era suposto escrever Afonso mas escrevi mal</td>
            <td class="tg-0lax">500</td>
            <td class="tg-0lax">Botão</td>
        </tr>
    </tbody>
</table>
<script charset="utf-8">
    var TGSort =
        window.TGSort ||
        (function (n) {
            "use strict";
            function r(n) {
                return n ? n.length : 0;
            }
            function t(n, t, e, o = 0) {
                for (e = r(n); o < e; ++o) t(n[o], o);
            }
            function e(n) {
                return n.split("").reverse().join("");
            }
            function o(n) {
                var e = n[0];
                return (
                    t(n, function (n) {
                        for (; !n.startsWith(e); ) e = e.substring(0, r(e) - 1);
                    }),
                    r(e)
                );
            }
            function u(n, r, e = []) {
                return (
                    t(n, function (n) {
                        r(n) && e.push(n);
                    }),
                    e
                );
            }
            var a = parseFloat;
            function i(n, r) {
                return function (t) {
                    var e = "";
                    return (
                        t.replace(n, function (n, t, o) {
                            return (e = t.replace(r, "") + "." + (o || "").substring(1));
                        }),
                        a(e)
                    );
                };
            }
            var s = i(/^(?:\s*)([+-]?(?:\d+)(?:,\d{3})*)(\.\d*)?$/g, /,/g),
                c = i(/^(?:\s*)([+-]?(?:\d+)(?:\.\d{3})*)(,\d*)?$/g, /\./g);
            function f(n) {
                var t = a(n);
                return !isNaN(t) && r("" + t) + 1 >= r(n) ? t : NaN;
            }
            function d(n) {
                var e = [],
                    o = n;
                return (
                    t([f, s, c], function (u) {
                        var a = [],
                            i = [];
                        t(n, function (n, r) {
                            (r = u(n)), a.push(r), r || i.push(n);
                        }),
                            r(i) < r(o) && ((o = i), (e = a));
                    }),
                    r(
                        u(o, function (n) {
                            return n == o[0];
                        })
                    ) == r(o)
                        ? e
                        : []
                );
            }
            function v(n) {
                if ("TABLE" == n.nodeName) {
                    for (
                        var a = (function (r) {
                                var e,
                                    o,
                                    u = [],
                                    a = [];
                                return (
                                    (function n(r, e) {
                                        e(r),
                                            t(r.childNodes, function (r) {
                                                n(r, e);
                                            });
                                    })(n, function (n) {
                                        "TR" == (o = n.nodeName) ? ((e = []), u.push(e), a.push(n)) : ("TD" != o && "TH" != o) || e.push(n);
                                    }),
                                    [u, a]
                                );
                            })(),
                            i = a[0],
                            s = a[1],
                            c = r(i),
                            f = c > 1 && r(i[0]) < r(i[1]) ? 1 : 0,
                            v = f + 1,
                            p = i[f],
                            h = r(p),
                            l = [],
                            g = [],
                            N = [],
                            m = v;
                        m < c;
                        ++m
                    ) {
                        for (var T = 0; T < h; ++T) {
                            r(g) < h && g.push([]);
                            var C = i[m][T],
                                L = C.textContent || C.innerText || "";
                            g[T].push(L.trim());
                        }
                        N.push(m - v);
                    }
                    t(p, function (n, t) {
                        l[t] = 0;
                        var a = n.classList;
                        a.add("tg-sort-header"),
                            n.addEventListener("click", function () {
                                var n = l[t];
                                !(function () {
                                    for (var n = 0; n < h; ++n) {
                                        var r = p[n].classList;
                                        r.remove("tg-sort-asc"), r.remove("tg-sort-desc"), (l[n] = 0);
                                    }
                                })(),
                                    (n = 1 == n ? -1 : +!n) && a.add(n > 0 ? "tg-sort-asc" : "tg-sort-desc"),
                                    (l[t] = n);
                                var i,
                                    f = g[t],
                                    m = function (r, t) {
                                        return n * f[r].localeCompare(f[t]) || n * (r - t);
                                    },
                                    T = (function (n) {
                                        var t = d(n);
                                        if (!r(t)) {
                                            var u = o(n),
                                                a = o(n.map(e));
                                            t = d(
                                                n.map(function (n) {
                                                    return n.substring(u, r(n) - a);
                                                })
                                            );
                                        }
                                        return t;
                                    })(f);
                                (r(T) || r((T = r(u((i = f.map(Date.parse)), isNaN)) ? [] : i))) &&
                                    (m = function (r, t) {
                                        var e = T[r],
                                            o = T[t],
                                            u = isNaN(e),
                                            a = isNaN(o);
                                        return u && a ? 0 : u ? -n : a ? n : e > o ? n : e < o ? -n : n * (r - t);
                                    });
                                var C,
                                    L = N.slice();
                                L.sort(m);
                                for (var E = v; E < c; ++E) (C = s[E].parentNode).removeChild(s[E]);
                                for (E = v; E < c; ++E) C.appendChild(s[v + L[E - v]]);
                            });
                    });
                }
            }
            n.addEventListener("DOMContentLoaded", function () {
                for (var t = n.getElementsByClassName("tg"), e = 0; e < r(t); ++e)
                    try {
                        v(t[e]);
                    } catch (n) {}
            });
        })(document);
</script>

    <?php
}

function cmp_adicionar_progresso() {
    ?>
    <style>
        .title{
            text-align: center;
            font-size: 500%;
        }
        .description{
            text-align: center;
            font-size: 120% !important;
        }
        .container{
            justify-content: center;
            align-items: center;
            display: flex;
            gap: 20%;
            margin-top: 7vh;
        }
        .form{
            width: 100%;
            height: 40vh;
            justify-content: center;
            /* display: flex; */
            padding-top: 2%;
        }
        .titleForm {
            font-size: 150%;
        }
        .form-container {
        width: 400px;
        border-radius: 0.75rem;
        background-color: rgba(17, 24, 39, 1);
        padding: 2rem;
        color: rgba(243, 244, 246, 1);
        }

        .titleForm {
        text-align: center;
        font-size: 1.5rem;
        line-height: 0.7rem;
        font-weight: 700;
        }

        .form {
        margin-top: 1.5rem;
        }

        .input-group {
        margin-top: 0.25rem;
        font-size: 0.875rem;
        line-height: 1.25rem;
        }

        .input-group label {
        display: block;
        color: rgba(156, 163, 175, 1);
        margin-bottom: 4px;
        }

        .input-group input {
        width: 100%;
        border-radius: 0.375rem;
        border: 1px solid rgba(55, 65, 81, 1);
        outline: 0;
        background-color: rgba(17, 24, 39, 1);
        padding: 0.75rem 1rem;
        color: rgba(243, 244, 246, 1);
        }

        .input-group input:focus {
        border-color: rgba(167, 139, 250);
        }

        .sign {
        display: block;
        width: 100%;
        background-color: rgba(167, 139, 250, 1);
        padding: 0.75rem;
        text-align: center;
        color: rgba(17, 24, 39, 1);
        border: none;
        border-radius: 0.375rem;
        font-weight: 600;
        }

        .tarefa {
            margin-top: 265px;
        }

        .contacto {
            margin-top: 20px;
        }
    </style>
    <h1 class="title">Adicionar Progresso</h1>
    <p class="description">Aqui podes adicionar pontos à tua conta ao colocares o que já fizeste</p>
    <div class="container">
    <div class="form-container">
	<p class="titleForm">Fizeste uma tarefa?</p>
	<form class="form">
		<div class="input-group">
			<label for="tarefa">Qual?</label>
			<input type="text" name="tarefa" id="tarefa">
		</div>
		<button class="sign tarefa">Enviar</button>
	</form>
    </div>
        <div class="form-container">
	<p class="titleForm">Fizeste um contacto?</p>
	<form class="form">
		<div class="input-group">
			<label for="cliente">Quem foi?</label>
			<input type="text" name="cliente" id="cliente" placeholder="">
		</div>
		<div class="input-group">
			<label for="tipo">Como contactaste?</label>
			<input type="text" name="tipo" id="tipo" placeholder="">
		</div>
        <div class="input-group">
			<label for="estado">Em que estado está?</label>
			<input type="text" name="estado" id="estado" placeholder="">
		</div>
        <div class="input-group">
			<label for="existência">Cliente novo ou estava no notion?</label>
			<input type="text" name="existência" id="existência" placeholder="">
		</div>
		<button class="sign contacto">Enviar</button>
	</form>
    </div>
    </div>
    <?php
}