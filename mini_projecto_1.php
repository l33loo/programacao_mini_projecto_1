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

enum Account: string {
    case Current = 'current';
    case Savings = 'savings';
}

enum Transaction: string {
    case Deposit = 'deposit';
    case Withdrawal = 'withdrawal';
}

function createAccount(float $ceiling, string $holder, Account $acctType, float $balance = 0.0): array {
    $ceil = 0;
    if ($acctType === Account::Current && $ceiling !== 0) {
        $ceil = abs($ceiling);
    }
    $absBalance = abs($balance);
    $creationDate = time();
    $accountArray = array(
        'balance' => $absBalance,
        'holder' => $holder,
        'type' => $acctType->value,
        'date' => $creationDate,
        'ceiling' => $ceiling,
    );

    if($absBalance > 0) {
        $accountArray['transactions'][] = array(
            'date' => $creationDate,
            'type' => Transaction::Deposit->value,
            'amount' => $absBalance,
        );
    }
    return $accountArray;
}

$newAcct = createAccount(500, 'Lila', Account::Current, 1000);
print_r($newAcct);


function addDeposit(float $amount, array &$transactions, float &$balance): void {
    $absAmount = abs($amount);
    $timeDeposit = time();
    $deposit = array(
        'date' => $timeDeposit,
        'type' => Transaction::Deposit->value,
        'amount' => $absAmount,
    );
    $transactions[] = $deposit;
    $balance = $absAmount + $balance;
    echo "balance <3: $balance\n";
}

addDeposit(550, $newAcct['transactions'], $newAcct['balance']);
print_r($newAcct);

function withdraw(float $ceiling, float &$balance, float $amount, array &$transactions) {
    $absAmount = abs($amount);
    $newBalance = $balance - $absAmount;

    if ($newBalance < -$ceiling) {
        echo "Withdrawal declined: insufficient funds\n";
        return;
    }

    $withdrawal = array(
        'date'=> time(),
        'type' => Transaction::Withdrawal->value,
        'amount' => $absAmount,
    );
    $transactions[] = $withdrawal;
    $balance = $newBalance;
}

withdraw($newAcct['ceiling'], $newAcct['balance'], 430.0, $newAcct['transactions']);
print_r($newAcct);
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

        if ($transaction['type'] === Transaction::Deposit->value) {
            $balanceAccumulator = $balanceAccumulator + $transaction['amount'];
        } elseif ($transaction['type'] === Transaction::Withdrawal->value) {
            $balanceAccumulator = $balanceAccumulator - $transaction['amount'];
        }
    }

    echo "The balance on $date was $balanceAccumulator\n";
}

// Output = 1120
balanceOnDate(date('ymd', time()), $newAcct['date'], $newAcct['transactions']);
// Output = Balance not found. Date (October 10, 2013) precedes account creation.
balanceOnDate('October 10, 2013', $newAcct['date'], $newAcct['transactions']);

function acctStatement(array $account, string $startDate, string $endDate): void {
    $holder = $account['holder'];
    $type = $account['type'];
    $transactions = $account['transactions'];
    $statement = "Holder: $holder\nType: $type\nTransactions:\n\tDATE\t\t\tTRANSACTION\tAMOUNT\tBALANCE\n";
    $balance = 0;
    
    for($i = 0; $i < count($transactions); $i++){
        $transaction = $transactions[$i];
        $type = $transaction['type'];
        $amount = $transaction ['amount'];
        if ($type === Transaction::Deposit->value) {
            $balance += $amount;
        } elseif ($type === Transaction::Withdrawal->value) {
            $balance -= $amount;
        }
        $date = $transaction['date'];
        $dateString = date('ymd', $date);
        $dateWithoutTime = strtotime($dateString);
        $startDateUnix = strtotime($startDate);
        $endDateUnix = strtotime($endDate);
        echo "END DATE <3: $endDateUnix\nTRANSACTION DATE: $date\n";
        if($dateWithoutTime >= $startDateUnix && $dateWithoutTime <= ($endDateUnix + 3600 * 24 - 1)) {
            // Put full date and time for statement "2023-10-09, 8:59:40"
            $dateStr = date('Y-m-d, G:i:s', $date);
            $tab = "\t";
            if ($type === Transaction::Deposit->value) {
                $tab .= "\t";
            }
            $transString = ($i + 1) . ".\t$dateStr\t$type$tab$amount\t$balance\n";
            $statement = $statement . $transString;
        }
    }

    echo $statement;
}

acctStatement($newAcct, "October 11, 2013", "October 11, 2023");
