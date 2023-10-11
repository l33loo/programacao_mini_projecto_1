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
    $timeDeposit = time();
    $deposit = array('date' => $timeDeposit, 'type' => 'deposit', 'amount' => $amount);
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
        echo "Withdrawal declined: insufficient funds\n";
        return;
    }

    $withdraw = array('date'=> time(), 'type' => 'withdraw', 'amount' => $amount);
    $transactions[] = $withdraw;
    $balance = $balance + $amount;
}

withdraw($newAcct['ceiling'], $newAcct['balance'], 430.0, $newAcct['transactions']);
// Declined due to insufficient funds
withdraw($newAcct['ceiling'], $newAcct['balance'], 5000.0, $newAcct['transactions']);
print_r($newAcct); 

function balanceOnDate(string $date, int $acctCreationDate, array &$transactions): void {
    $balanceAccumulator = 0;
    $filterDateUnix = strtotime($date);

    if ($filterDateUnix < $acctCreationDate) {
        echo "Balance not found. Date ($date) precedes account creation.\n";
        return;
    }

    foreach ($transactions as $transaction) {
        $transactionDateString = date('M-D-Y', $transaction['date']);
        $transactionDateUnixWithoutTime = strtotime($transactionDateString);

        if ($transactionDateUnixWithoutTime > $filterDateUnix) {
            break;
        }

        if ($transaction['type'] === 'deposit') {
            $balanceAccumulator = $balanceAccumulator + $transaction['amount'];
        } elseif ($transaction['type'] === 'withdraw') {
            $balanceAccumulator = $balanceAccumulator - $transaction['amount'];
        }
    }

    echo "The balance on $date was $balanceAccumulator\n";
}

// Output = 1120
balanceOnDate(date('ymd', time()), $newAcct['date'], $newAcct['transactions']);
// Output = Balance not found. Date (October 10, 2013) precedes account creation.
balanceOnDate('October 10, 2013', $newAcct['date'], $newAcct['transactions']);



// // Extrato de conta
// Holder:
// Type
// Transactions:
// Date     Transaction     Amount     Balance
// ...


function acctStatement(array $account): void {
    $holder = $account['holder'];
    $type = $account['type'];
    $transactions = $account['transactions'];
    $statement = "Holder: $holder\nType: $type\nTransactions:\n\tDATE\t\tTRANSACTION\tAMOUNT\tBALANCE\n";
    $balance = 0;
    
    for($i = 0; $i < count($transactions); $i++){
        $transaction = $transactions[$i];
        $date = $transaction['date'];
        // Put full date and time for statement "2023-10-09, 8:59:40"
        $dateStr = date('m-d-Y', $date);
        $type = $transaction['type'];
        $amount = $transaction ['amount'];

        if ($type === 'deposit') {
            $balance += $amount;
        } elseif ($type === 'withdraw') {
            $balance -= $amount;
        }

        $transString = ($i + 1) . ".\t$dateStr\t$type\t$amount\t$balance\n";
        $statement = $statement . $transString;
    }

    echo $statement;
}

acctStatement($newAcct);
