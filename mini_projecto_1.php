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

function createAccount(float $balance, string $holder, string $type): array {
    $initialtransaction = null;
    return array(
        'balance' => $balance,
        'holder' => $holder,
        'type' => $type,
        'date' => time(),
        'transactions' => array($transactions),
    );
}