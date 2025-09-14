# WhatsApp Form Manager

Plugin WordPress para criar um formulário que envia mensagens para WhatsApp. Interface de administração para gerenciar campos (similar ao Contact Form 7) e configurar estilos sem editar código.

## Features
- Adicione/edite/exclua campos via interface visual.
- Escolha placeholders, se o campo é obrigatório, tipo do campo.
- Configure cores, bordas, alinhamento e texto do botão sem código.
- Suporte opcional a reCAPTCHA.
- Shortcode: `[whatsapp_form]`

## Estrutura do repositório
- `whatsapp-form-plugin-advanced/`
  - `includes/` - arquivos PHP de opções e admin pages
  - `assets/` - JS/CSS para admin e front
  - `whatsapp-form-plugin-advanced.php` - plugin principal

## Instalação
1. Envie o plugin (zip) em **Plugins → Adicionar novo → Enviar plugin**.
2. Ative.
3. Acesse o menu **WhatsApp Form** para configurar campos e estilos.
4. Use o shortcode `[whatsapp_form]`.

## Observações
- O plugin abre o link `https://wa.me/<phone>?text=...`. Use o campo Número com o DDI (ex: 5515997064008).
- reCAPTCHA precisa de chaves válidas para o domínio.
