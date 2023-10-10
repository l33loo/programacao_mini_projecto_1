<?php

// O programa fará a gestão de contas de uma pessoa

// as contas tem os seguintes dados:

//     Saldo
//     Titular
//     Tipo
//     Data de Abertura
//     Lista de Movimentos
//     Plafond

// Um movimento é composto por

//     Data de Movimento
//     Tipo de Movimento
//     Montante


// Por favor, implementar as seguintes funções:

// Fazer um depósito
// Consiste em adicionar um movimento de credito na conta e ajustar o saldo

// Fazer um levantamento
// Consiste em adicionar um movimento de débito na conta e ajustar o saldo. As contas do tipo ORDEM podem ter saldo negativo até atingir o plafond, as contas a prazo não podem ter saldo negativo

// Fazer uma função que devolve o saldo numa determinada data

// Fazer o extracto de conta
// Deve apresentar uma listagem com todos os movimentos entre um intervalo de datas, ordenados por data e apresentar o montante, tipo, data, e saldo acumulado


//////////////////////////////////////////////////////////////////

// Array:
//     (float) Saldo
//     (string) Titular
//     (string) Tipo
//     (data - int -> strtotime) Data de Abertura
//     Array: Lista de Movimentos
//          (data - int -> strtotime) Data de Movimento
//          (string) Tipo de Movimento
//          (float) Montante
//     (float) Plafond


// funcoes:
// - criar uma conta
// - deposito
// - levantamento
// - devolver o saldo numa determinada data
// - extracto de conta

function createAccount(float $ceiling, string $holder, string $acctType,float $balance = 0.0): array {
    $accountArry = array(
        'balance' => $balance,
        'holder' => $holder,
        'type' => $acctType,
        'date' => time(),
        'ceiling' => $ceiling,
        'transactions' => array(),
    );

    if($balance > 0) {
        $accountArry['transactions'] [] = array('date' => time(), 'type' => 'deposit', 'amount' => $balance,);
    }
    return $accountArry;
}

$newAcct = createAccount(500, 'Lila', 'current', 1000);
print_r($newAcct);


function addDeposit(float $amount, array &$transactions, float &$balance): void {
    $deposit = array('date' => time(), 'type' => 'deposit', 'amount' => $amount);
    $transactions[] = $deposit;
    $balance = $amount + $balance;
    echo "balance <3: $balance\n";
}
$deposit = addDeposit(550, $newAcct['transactions'], $newAcct['balance']);

echo gettype($deposit);
print_r($newAcct);

function withdraw(float $ceiling, float &$balance, float $amount, array &$transactions) { 
    $newBalance = $balance - $amount;

    if ($newBalance < $ceiling) {
        echo "Withdrawal declined: insufficient funds";
        return;
    }

    $withdraw = array('date'=> time(), 'type' => 'withdraw', 'amount' => $amount);
    $transactions[] = $withdraw;
    $balance = $balance + $amount;
}

withdraw($newAcct['ceiling'], $newAcct['balance'], 430.0, $newAcct['transactions']);
print_r($newAcct); 

function balanceOnDate(string $date, array &$transactions): void {
    $filterDateUnix = strtotime($date);
    echo "DATE: $filterDateUnix\n";

    // $filterDateUnix = time() + 100000;

    $balancaAccumulator = 0;

    foreach ($transactions as $transaction) {
        if ($transaction['date'] > $filterDateUnix) {
            break;
        }

        if ($transaction['type'] === 'deposit') {
            $balancaAccumulator = $balancaAccumulator + $transaction['amount'];
        } elseif ($transaction['type'] === 'withdraw') {
            $balancaAccumulator = $balancaAccumulator - $transaction['amount'];
        }
    }

    echo "The balance on $date was $balancaAccumulator\n";
}

balanceOnDate('October 11, 2023', $newAcct['transactions']);




// // Extrato de conta
// Date     Transaction     Amount     Balance
