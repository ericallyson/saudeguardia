# Saúde Guardiã - Plataforma de Acompanhamento Médico Longitudinal

## Visão Geral

A **Saúde Guardiã** é uma plataforma inovadora de acompanhamento e monitoramento médico longitudinal que revoluciona o modelo tradicional de consultas-retornos, mantendo um contato permanente entre médico e paciente através de tecnologia integrada.

## Funcionalidades Principais

### 🔄 Monitoramento Contínuo
- Acompanhamento longitudinal individualizado para cada paciente
- Contato permanente entre consultas presenciais
- Disparos automáticos de lembretes e avisos via WhatsApp

### 📱 Integração com WhatsApp
- Lembretes automáticos nos dias e horários programados
- Monitoramento de cumprimento de metas estabelecidas
- Respostas dos pacientes com confirmação (check) de realização
- Mensagens de reforço positivo e incentivo (nunca críticas)

### 📊 Relatórios Inteligentes
- Geração automática de relatórios baseados nas respostas dos pacientes
- Relatórios semanais em PDF enviados todas as sextas-feiras
- Análise de engajamento e evolução dos pacientes
- Dashboards com métricas em tempo real

### 🎯 Variáveis Monitoradas
- **Peso corporal**
- **Circunferência abdominal**
- **Pressão arterial**
- **Glicemias**
- **Atividade física**
- **Qualidade do sono**
- **Orientações alimentares**
- **Uso de medicações**
- **Realização de exames**
- **Lembretes de consultas**

## Estrutura do Projeto

### Páginas Desenvolvidas

1. **Dashboard Principal** (`index.html`)
   - Visão geral dos pacientes ativos
   - Métricas de engajamento
   - Alertas e notificações
   - Gráficos de evolução

2. **Gestão de Pacientes** (`pacientes.html`)
   - Lista completa de pacientes
   - Status de cada paciente
   - Filtros e busca

3. **Detalhes do Paciente** (`paciente-detalhes.html`)
   - Informações completas do paciente
   - Progresso das metas
   - Histórico de atividades
   - Métricas individuais

4. **Cadastro de Paciente** (`novo-paciente.html`)
   - Formulário completo de cadastro
   - Configuração de metas personalizadas
   - Integração com WhatsApp
   - Definição de frequência de lembretes

5. **Relatórios** (`relatorios.html`)
   - Filtros avançados
   - Estatísticas de relatórios
   - Download de PDFs
   - Visualização de dados

6. **Configurações** (`configuracoes.html`)
   - Perfil do médico
   - Integração WhatsApp Business
   - Configurações de notificações
   - Configurações do sistema

## Design e Interface

### Paleta de Cores (Tons Pastéis)
- **Bege**: `#F5F5DC` - Cor de fundo principal
- **Marrom**: `#A0522D` - Elementos de destaque e botões
- **Cinza Claro**: `#D3D3D3` - Bordas e separadores
- **Cinza Escuro**: `#A9A9A9` - Texto secundário

### Tecnologias Utilizadas
- **HTML5** - Estrutura semântica
- **Tailwind CSS** - Framework de estilização
- **CSS Customizado** - Personalização da paleta de cores
- **SVG Icons** - Ícones vetoriais responsivos

## Fluxo de Trabalho

### 1. Primeira Consulta
- Cadastro do paciente na plataforma
- Configuração do plano personalizado
- Definição de metas de peso e hábitos
- Configuração da frequência de registros

### 2. Monitoramento Automático
- Mensagens automáticas via WhatsApp
- Registro de peso, pressão e outras métricas
- Respostas às metas prescritas
- Linguagem simples e frequência personalizada

### 3. Acompanhamento Motivacional
- Mensagens de voz semanais do médico
- Orientações baseadas no painel de controle
- Elogios e reforços positivos
- Ajustes de conduta quando necessário

### 4. Relatórios e Evolução
- Identificação de pacientes engajados
- Detecção de quem precisa de atenção
- Acompanhamento da evolução do peso
- Dados para ajuste de condutas

## Instalação e Uso

1. **Clone ou baixe os arquivos do projeto**
2. **Abra o arquivo `index.html` em um navegador web**
3. **Navegue pelas diferentes páginas usando o menu lateral**
4. **Personalize as configurações conforme necessário**

## Estrutura de Arquivos

```
saude-guardia/
├── index.html              # Dashboard principal
├── pacientes.html          # Lista de pacientes
├── paciente-detalhes.html  # Detalhes do paciente
├── novo-paciente.html      # Cadastro de paciente
├── relatorios.html         # Relatórios e análises
├── configuracoes.html      # Configurações do sistema
├── css/
│   └── style.css          # Estilos personalizados
├── img/
│   ├── doctor.jpg         # Avatar do médico
│   ├── patient1.jpg       # Avatar paciente 1
│   ├── patient2.jpg       # Avatar paciente 2
│   └── patient3.jpg       # Avatar paciente 3
└── README.md              # Documentação
```

## Próximos Passos

### Funcionalidades Futuras
- **Integração com smartwatches** para coleta automática de dados
- **API para integração com sistemas hospitalares**
- **Aplicativo móvel** para pacientes
- **Inteligência artificial** para análise preditiva
- **Telemedicina integrada** para consultas remotas

### Melhorias Técnicas
- **Backend em PHP/MySQL** para funcionalidade completa
- **Sistema de autenticação** e segurança
- **API REST** para integração com terceiros
- **Notificações push** em tempo real
- **Backup automático** de dados

## Suporte e Contato

Para dúvidas, sugestões ou suporte técnico, entre em contato através dos canais oficiais da plataforma Saúde Guardiã.

---

**Desenvolvido com foco na melhoria da qualidade de vida dos pacientes através do acompanhamento médico contínuo e personalizado.**
