# Geração de cronograma

Este documento descreve como funciona a geração automática de cronogramas.

## Janela de geração
- O cronograma é gerado para 15 dias a partir da data de início.
- Se não for informada data, usa o dia atual.
- Antes de gerar, pode limpar sessões não finalizadas no período.

## Disponibilidade diária
- A disponibilidade do usuário é lida do campo `horario_semanal`.
- Cada dia recebe a soma de minutos disponível e as sessões são alocadas até esse limite.

## Duração das sessões
- `teoria`: 30 minutos
- `exercicio`: 30 minutos
- `revisao`: 15 minutos

## Regras de tipos
- Se `teoria_finalizada` for **false**, o assunto só gera `teoria` e `revisao`.
- Se `teoria_finalizada` for **true**, o assunto gera `exercicio` e `revisao`.

## Prioridade (curva de esquecimento)
Cada assunto recebe uma pontuação:

$$
score = taxa\_erro + 0.5 \cdot esquecimento - 0.05 \cdot sessoes\_agendadas
$$

Onde:
- $taxa\_erro = \frac{erros}{acertos + erros}$
- $esquecimento = \min(1, \frac{dias\_desde\_ultima}{7})$

Assuntos com maior score são priorizados.

## Distribuição e alternância
- **Tipos**: não permite 3 sessões seguidas do mesmo tipo.
- **Matéria**: não permite mais de 2 sessões seguidas da mesma matéria.
- Todos os assuntos participam do ciclo; quando todos são usados, o ciclo reinicia.

## Persistência
As sessões são gravadas com:
- `data`, `tipo`, `horas`, `finalizado`, `assunto_id`.
