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
        'ceiling' => $ceil,
        'transactions' => array(),
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

function addDeposit(array &$account, float $amount): void {
    $transactions = &$account['transactions'];
    $balance = &$account['balance'];
    $absAmount = abs($amount);
    $timeDeposit = time();
    $deposit = array(
        'date' => $timeDeposit,
        'type' => Transaction::Deposit->value,
        'amount' => $absAmount,
    );
    $transactions[] = $deposit;
    $balance = $absAmount + $balance;
}

function withdraw(array &$account, float $amount) {
    $ceiling = $account['ceiling'];
    $balance = &$account['balance'];
    $transactions = &$account['transactions'];
    $absAmount = abs($amount);
    $newBalance = $balance - $absAmount;

    if ($newBalance < -$ceiling) {
        echo "Withdrawal of $absAmount declined: insufficient funds\n";
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

function balanceOnDate(array $account, string $date): void {
    $acctCreationDate = $account['date'];
    $transactions = &$account['transactions'];
    $balanceAccumulator = 0;
    $filterDateUnix = strtotime($date);
    $acctDateWithoutTime = removeTimeFromDate($acctCreationDate);

    if ($filterDateUnix < $acctDateWithoutTime) {
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
            $balanceAccumulator += $transaction['amount'];
        } elseif ($transaction['type'] === Transaction::Withdrawal->value) {
            $balanceAccumulator -= $transaction['amount'];
        }
    }

    echo "The balance on $date was $balanceAccumulator\n";
}

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
        $dateWithoutTime = removeTimeFromDate($date);
        $startDateUnix = strtotime($startDate);
        $endDateUnix = strtotime($endDate);

        if($dateWithoutTime >= $startDateUnix && $dateWithoutTime <= ($endDateUnix + 3600 * 24 - 1 /*round to end of day*/)) {
            $dateStr = date('Y-m-d, G:i:s', $date);
            $tab = "\t";
            if ($type === Transaction::Deposit->value) {
                $tab .= "\t";
            }
            $transString = ($i + 1) . ".\t$dateStr\t$type$tab$amount\t$balance\n";
            $statement .= $transString;
        }
    }

    echo $statement;
}

function removeTimeFromDate(int $timestamp): int {
    $dateWithoutTime = date("Y-m-d", $timestamp);
    $timestampWithoutTime = strtotime($dateWithoutTime);
    return $timestampWithoutTime;
}

$newCurrentAcct = createAccount(500, 'Lila', Account::Current, 1000);
print_r($newCurrentAcct);

addDeposit($newCurrentAcct, 550);
print_r($newCurrentAcct);

withdraw($newCurrentAcct, 430.0);
print_r($newCurrentAcct);
withdraw($newCurrentAcct, 1620.0);
// Declined due to insufficient funds
withdraw($newCurrentAcct, 1.0);
print_r($newCurrentAcct);

// Output = -500
balanceOnDate($newCurrentAcct, date('Y-m-d', time()));
// Output = Balance not found. Date (October 10, 2013) precedes account creation.
balanceOnDate($newCurrentAcct, 'October 10, 2013');

//// Output =
// Holder: Lila
// Type: current
// Transactions:
//         DATE                    TRANSACTION     AMOUNT  BALANCE
// 1.      2023-10-12, 8:39:18     deposit         1000    1000
// 2.      2023-10-12, 8:39:18     deposit         550     1550
// 3.      2023-10-12, 8:39:18     withdrawal      430     1120
// 4.      2023-10-12, 8:39:18     withdrawal      1620    -500
acctStatement($newCurrentAcct, date("Y-m-d", time() - 24 * 3600), date("Y-m-d", time() + 24 * 3600));

$newSavingsAcct = createAccount(500, 'Lila', Account::Savings);
print_r($newSavingsAcct);

// Declined due to insufficient funds
withdraw($newSavingsAcct, 1.0);
print_r($newSavingsAcct);