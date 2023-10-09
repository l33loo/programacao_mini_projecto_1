# Mini Projecto 1

## Applicacao Bancaria

O programa fara a gestao de contas de uma pessoa.
As contas tem os seguintes dados:
- saldo
- titular
- tipo
- data de abertura
- lista de movimentos

Um movimento ee composto po:
- data de movimento
- tipo de movimento
- montante

For favor, implementar as sequintes funcoes:
- fazer um deposito
    - consiste em adicionar um movimento de credito na conta e ajustar o saldo
- fazer um levantamento
    - consiste em adicionar um movimento de debito na conta e ajustar o saldo. As contas do tipu ORDEM podem ter saldo negativo at/ atingir o plafond, as contas a prazo naos podem ter saldo negativo
- fazer o extracto de conta
    - deve apresentar uma listagem com todos os movimentos entre um intervalo de dates, ordernados por data e apresentar o montante, tipo, data, e saldo acumulado