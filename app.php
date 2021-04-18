<?php

    class Dashboard{
        public $data_inicio;
        public $data_fim;
        public $num_vendas;
        public $num_total;
        public $clientes_ativos;
        public $clientes_inativos;
        public $elogios;
        public $sugestoes;
        public $reclamacoes;
        public $despesas;

        public function __get($atributo){
            return $this->$atributo;
        }

        public function __set($atributo, $valor){
            $this->$atributo = $valor;
            return $this;
        }
    }

    class Conexao {
        private $host = 'localhost';
        private $dbname = 'dashboard';
        private $user = 'root';
        private $pass = '';

        public function conectar()
        {
            try{
                $conexao = new PDO(
                    "mysql:host=$this->host;dbname=$this->dbname",
                    "$this->user",
                    "$this->pass"
                );

                

                return $conexao;
            }catch(PDOException $e){
                echo '<p class="danger">' . $e->getMessage() . '</p>';
            }
        }

        
    }

    class Bd{
        private $conexao;
        private $dashboard;

        public function __construct(Conexao $conexao, Dashboard $dashboard){
            $this->conexao = $conexao->conectar();
            $this->dashboard = $dashboard;
        }

        public function getNumeroVendas(){
            $query = '
            select 
                count(*) as numero_vendas
            from 
                tb_vendas 
            where 
                data_venda between :data_inicio and :data_fim';

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':data_inicio',$this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_fim',$this->dashboard->__get('data_fim'));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;
        }

        public function getClientesAtivos(){
            $query = '
            select 
                count(cliente_ativo) clientes
            from 
                tb_clientes 
            where
                cliente_ativo = 1
            group by 
                cliente_ativo ';

            $stmt = $this->conexao->prepare($query);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->clientes;
        }

        public function getClientesInativos(){
            $query = '
            select 
                count(cliente_ativo) clientes
            from 
                tb_clientes 
            where
                cliente_ativo = 0
            group by 
                cliente_ativo ';

            $stmt = $this->conexao->prepare($query);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->clientes;
        }

        public function getReclamacoes(){
            $query = '
            select 
                count(tipo_contato) total
            from 
                tb_contatos
            where
                tipo_contato = 1
            group by 
                tipo_contato ';

            $stmt = $this->conexao->prepare($query);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total;
        }

        public function getElogios(){
            $query = '
            select 
                count(tipo_contato) total
            from 
                tb_contatos
            where
                tipo_contato = 2
            group by 
                tipo_contato ';

            $stmt = $this->conexao->prepare($query);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total;
        }

        public function getSugestacoes(){
            $query = '
            select 
                count(tipo_contato) total
            from 
                tb_contatos
            where
                tipo_contato = 3
            group by 
                tipo_contato ';

            $stmt = $this->conexao->prepare($query);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total;
        }


        public function getTotalVendas(){
            $query = '
            select 
                sum(total) as total_vendas
            from 
                tb_vendas 
            where 
                data_venda between :data_inicio and :data_fim';

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':data_inicio',$this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_fim',$this->dashboard->__get('data_fim'));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
        }

        public function getDespesas(){
            $query = '
            select 
                sum(total) as total_vendas
            from 
                tb_despesas 
            where 
                data_despesa between :data_inicio and :data_fim';

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':data_inicio',$this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_fim',$this->dashboard->__get('data_fim'));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
        }
    }

    $dashboard = new Dashboard();

    $conexao = new Conexao();

    

    if($_GET['acao'] == 'competencia'){

        $competencia = explode('-', $_GET["competencia"]);
        $ano = $competencia[0];
        $mes = $competencia[1];

        $diaMax = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

        $dashboard->__set('data_inicio', $ano . '-' . $mes . '-01');
        $dashboard->__set('data_fim',$ano . '-' . $mes . '-' . $diaMax);


        $bd = new Bd($conexao, $dashboard);
        $dashboard->__set('num_vendas',$bd->getNumeroVendas());
        $dashboard->__set('num_total',$bd->getTotalVendas());
        $dashboard->__set('despesas',$bd->getDespesas());
        
        echo json_encode($dashboard);


    }elseif($_GET['acao'] == 'cliente'){

        $bd = new Bd($conexao, $dashboard);
        $dashboard->__set('clientes_ativos',$bd->getClientesAtivos());
        $dashboard->__set('clientes_inativos',$bd->getClientesInativos());
        $dashboard->__set('elogios',$bd->getClientesAtivos());
        $dashboard->__set('reclamacoes',$bd->getClientesInativos());
        $dashboard->__set('sugestoes',$bd->getClientesAtivos());
        
        echo json_encode($dashboard);

    }

?>