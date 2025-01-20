# Changelog

Todas as alterações efetuadas no componente `clicksign` serão documentadas neste arquivo.
## 0.2.27-alpha - 2025-01-20
- Adicionado método para atualizar a data limite para assinatura. A data limite para assinatura do documento poderá ser prorrogado
para até 90 dias após a data de upload do documento.

## 0.2.14-alpha - 2024-04-26
- Adicionado os métodos *getDocumentSignDuration*, *setDocumentSignDuration*, *getUseIntegration* e *setUseIntegration*.

## 0.2.12-alpha - 2024-04-26
- Adicionado os parâmetros *documentSignDuration* e *useIntegration*.

## 0.2.5-alpha - 2024-01-31
- Refatorado os parâmetros *documentUrlVersion*, *listUrlVersion*, *notificationUrlVersion* e *signerUrlVersion*. Passaram-se a chamar *documentEndPoint*, *listEndPoint*, *notificationEndPoint* e *signerEndPoint* respectivamente.
- Refatorada a migration para criar a tabela apenas se a tabela não existir.

## 0.2.3-alpha - 2024-01-23
- Refatorada a publicação das migrations.

## 0.2.1-alpha - 2024-01-23
- Adicionado o suporte para os *tokens* da área de desenvolvimento (sandbox) e da área de produção.
- Conclusão da documentação do componente.

## 0.1-alpha - 2024-01-17
- Release inicial
