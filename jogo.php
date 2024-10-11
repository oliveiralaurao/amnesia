
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            margin-top: 20px;
        }
        .card {
            width: 100px;
            height: 150px;
            background-color: #007bff;
            display: inline-block;
            margin: 10px;
            text-align: center;
            vertical-align: top;
            cursor: pointer;
            font-size: 1.5rem;
            color: white;
            line-height: 150px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .hidden {
            background-color: #6c757d;
            color: transparent;
        }
        .game-info {
            margin-top: 20px;
        }
        .game-info p {
            font-size: 1.2rem;
        }
    </style>
    <title>Jogo da Memória</title>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        
      </ul>
      <form class="d-flex" role="search">
      
          <a class="nav-link active" aria-current="page" href="back/sair.php">Sair</a>
        
      </form>
    </div>
  </div>
</nav>
    <div class="container text-center">
        <h1>Jogo da Memória</h1>
        
        <!-- Seletor de nível de dificuldade -->
        <div class="form-group">
            <label for="nivel">Escolha o nível de dificuldade:</label>
            <select id="nivel" class="form-control" style="max-width: 200px; margin: 0 auto;">
                <option value="facil">Fácil</option>
                <option value="medio">Médio</option>
                <option value="dificil">Difícil</option>
            </select>
        </div>

        <!-- Seletor para modo de jogo -->
        <div class="form-group">
            <label for="modo">Escolha o modo de jogo:</label>
            <select id="modo" class="form-control" style="max-width: 200px; margin: 0 auto;">
                <option value="solo">Solo</option>
                <option value="dupla">Dupla</option>
            </select>
        </div>

        <!-- Contadores e temporizador -->
        <div class="game-info">
            <p>Erros: <span id="contador-erros">0</span></p>
            <p id="pontuacao-jogadores" style="display:none;">
                Pontuação Jogador 1: <span id="pontos-jogador1">0</span> | 
                Pontuação Jogador 2: <span id="pontos-jogador2">0</span>
            </p>
            <p>Tempo: <span id="temporizador">00:00</span></p>
        </div>

        <!-- Tabuleiro de jogo -->
        <div id="tabuleiro" class="text-center"></div>

        <!-- Botão para iniciar o jogo -->
        <button id="iniciar-jogo" class="btn btn-primary mt-4">Iniciar Jogo</button>
        <button id="reiniciar-jogo" class="btn btn-secondary mt-4" style="display:none;">Reiniciar Jogo</button>

        <!-- Histórico de jogos -->
        <div class="game-history mt-5">
            <h3>Histórico de Partidas</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Tempo</th>
                        <th>Erros</th>
                        <th>Nível</th>
                        <th>Pontuação (Jogador 1 / Jogador 2)</th>
                    </tr>
                </thead>
                <tbody id="historico-partidas">
                    <!-- Partidas anteriores serão carregadas aqui -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        let nivel = document.getElementById('nivel').value;
        let modo = document.getElementById('modo').value;
        let tabuleiro = document.getElementById('tabuleiro');
        let erros = 0;
        let jogadas = [];
        let cartasViradas = [];
        let pontosJogador1 = 0;
        let pontosJogador2 = 0;
        let vezJogador = 1; // Alterna entre jogador 1 e 2 no modo dupla
        let cronometro = null;
        let tempo = 0;

        document.getElementById('iniciar-jogo').addEventListener('click', iniciarJogo);
        document.getElementById('reiniciar-jogo').addEventListener('click', reiniciarJogo);

        function iniciarJogo() {
            nivel = document.getElementById('nivel').value;
            modo = document.getElementById('modo').value;
            erros = 0;
            jogadas = [];
            cartasViradas = [];
            pontosJogador1 = 0;
            pontosJogador2 = 0;
            vezJogador = 1;
            tempo = 0;

            document.getElementById('contador-erros').textContent = erros;
            document.getElementById('temporizador').textContent = '00:00';
            document.getElementById('pontos-jogador1').textContent = pontosJogador1;
            document.getElementById('pontos-jogador2').textContent = pontosJogador2;

            if (modo === 'dupla') {
                document.getElementById('pontuacao-jogadores').style.display = 'block';
            } else {
                document.getElementById('pontuacao-jogadores').style.display = 'none';
            }

            // Gerar cartas baseado no nível de dificuldade
            gerarTabuleiro(nivel);
            iniciarCronometro();

            document.getElementById('iniciar-jogo').style.display = 'none';
            document.getElementById('reiniciar-jogo').style.display = 'inline-block';
        }

        function gerarTabuleiro(nivel) {
            tabuleiro.innerHTML = '';
            let cartas = [];

            if (nivel === 'facil') {
                cartas = ['A', 'A', 'B', 'B', 'C', 'C', 'D', 'D'];
            } else if (nivel === 'medio') {
                cartas = ['A', 'A', 'B', 'B', 'C', 'C', 'D', 'D', 'E', 'E', 'F', 'F'];
            } else if (nivel === 'dificil') {
                cartas = ['A', 'A', 'B', 'B', 'C', 'C', 'D', 'D', 'E', 'E', 'F', 'F', 'G', 'G', 'H', 'H'];
            }

            cartas = embaralhar(cartas);

            cartas.forEach(carta => {
                let divCarta = document.createElement('div');
                divCarta.classList.add('card', 'hidden');
                divCarta.textContent = carta;
                divCarta.addEventListener('click', virarCarta);
                tabuleiro.appendChild(divCarta);
            });
        }

        function embaralhar(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        }

        function virarCarta() {
            if (cartasViradas.length < 2 && this.classList.contains('hidden')) {
                this.classList.remove('hidden');
                cartasViradas.push(this);

                if (cartasViradas.length === 2) {
                    verificarPar();
                }
            }
        }

        function verificarPar() {
    if (cartasViradas[0].textContent === cartasViradas[1].textContent) {
        if (modo === 'dupla') {
            if (vezJogador === 1) {
                pontosJogador1++;
                document.getElementById('pontos-jogador1').textContent = pontosJogador1;
            } else {
                pontosJogador2++;
                document.getElementById('pontos-jogador2').textContent = pontosJogador2;
            }
        }
        cartasViradas = [];
    } else {
        setTimeout(() => {
            cartasViradas.forEach(carta => carta.classList.add('hidden'));
            cartasViradas = [];
        }, 1000);
        erros++;
        document.getElementById('contador-erros').textContent = erros;
    }

    // Alterna a vez no modo dupla
    if (modo === 'dupla') {
        vezJogador = vezJogador === 1 ? 2 : 1;
    }

    // Verifica se o jogo acabou (todas as cartas foram viradas)
    verificarFimDoJogo();
}

function verificarFimDoJogo() {
    const todasCartas = document.querySelectorAll('.card');
    const todasViradas = Array.from(todasCartas).every(carta => !carta.classList.contains('hidden'));

    if (todasViradas) {
        finalizarJogo();
    }
}


        function iniciarCronometro() {
            // Impede que múltiplos cronômetros sejam iniciados
            if (cronometro) return;

            cronometro = setInterval(() => {
                tempo++;
                let minutos = Math.floor(tempo / 60);
                let segundos = tempo % 60;
                document.getElementById('temporizador').textContent = 
                    (minutos < 10 ? '0' : '') + minutos + ':' + (segundos < 10 ? '0' : '') + segundos;
            }, 1000);
        }

        function reiniciarJogo() {
            clearInterval(cronometro);
            cronometro = null;
            tempo = 0;
            document.getElementById('temporizador').textContent = '00:00';
            document.getElementById('iniciar-jogo').style.display = 'inline-block';
            document.getElementById('reiniciar-jogo').style.display = 'none';
            tabuleiro.innerHTML = '';
            document.getElementById('contador-erros').textContent = '0';
            document.getElementById('pontos-jogador1').textContent = '0';
            document.getElementById('pontos-jogador2').textContent = '0';
        }

        // Adiciona uma partida ao histórico de jogos
        function adicionarAoHistorico() {
            const dataAtual = new Date();
            const dataFormatada = dataAtual.toLocaleDateString();
            const tempoJogo = document.getElementById('temporizador').textContent;
            const nivelJogo = document.getElementById('nivel').value;
            const errosTotais = document.getElementById('contador-erros').textContent;
            const pontos1 = document.getElementById('pontos-jogador1').textContent;
            const pontos2 = document.getElementById('pontos-jogador2').textContent;

            const historico = document.getElementById('historico-partidas');
            const linhaHistorico = document.createElement('tr');

            linhaHistorico.innerHTML = `
                <td>${dataFormatada}</td>
                <td>${tempoJogo}</td>
                <td>${errosTotais}</td>
                <td>${nivelJogo}</td>
                <td>${pontos1} / ${pontos2}</td>
            `;

            historico.appendChild(linhaHistorico);
        }

        function finalizarJogo() {
    clearInterval(cronometro);
    cronometro = null;

    adicionarAoHistorico();

    alert('Partida finalizada!'); 
}


    </script>
</body>
</html>

               
