document.addEventListener('DOMContentLoaded', function () {
  fetch('/dashboard/data.json')
    .then(response => response.json())
    .then(data => {
      // Função para criar um card Kanban
      function createKanbanCard(item, type) {
        const card = document.createElement('div');
        card.className = 'kanban-card';
        if (item.foto) {
          const img = document.createElement('img');
          img.src = 'user.svg';
          img.alt = item.nome || item.paciente;
          img.className = 'kanban-img';
          card.appendChild(img);
        }
        const textContainer = document.createElement('div');
        textContainer.className = 'text-container';

        if (item.nome) {
          if (type === 'novosMedicos') {
            textContainer.innerHTML += `<p><strong>${item.nome}</strong></p>`;
            textContainer.innerHTML += `<p>Especialidade: ${item.especialidade}</p>`;
          } else {
            textContainer.innerHTML += `<p><strong>${item.nome}</strong></p>`;
          }
        }
        if (type === 'agendamentos') {
          textContainer.innerHTML += `<p>Médico: ${item.medico}</p>`;
          textContainer.innerHTML += `<p>Modalidade: ${item.modalidade}</p>`;
          textContainer.innerHTML += `<p>Data/Hora: ${new Date(item.data).toLocaleDateString() == new Date().toLocaleDateString() ? "Hoje" : new Date(item.data).toLocaleDateString()} as ${item.horario}</p>`;
        } else if (type === 'novosPacientes') {
          textContainer.innerHTML += `<p>Queixa: ${item.queixa_principal}</p>`;
          textContainer.innerHTML += `<p>Data de Criação: ${item.dataCriacao}</p>`;
          textContainer.innerHTML += `<p>Idade: ${item.idade} anos</p>`;
        } else if (type === 'novosMedicos') {
          textContainer.innerHTML += `<p>Data de Criação: ${item.dataCriacao}</p>`;
          textContainer.innerHTML += `<p>Idade: ${item.idade} anos</p>`;
        } else if (type === 'acompanhamentosMedicos') {
          textContainer.innerHTML += `<p><strong>Paciente: ${item.paciente}</strong></p>`;
          textContainer.innerHTML += `<p>Descrição: ${item.descricao}</p>`;
          textContainer.innerHTML += `<p>Data: ${item.data}</p>`;
          textContainer.innerHTML += `<p>Médico: ${item.medico}</p>`;
        }

        card.appendChild(textContainer);
        return card;
      }

      // Adiciona dados aos respectivos containers
      function populateColumn(columnId, items, type) {
        const column = document.getElementById(columnId);
        items.forEach(item => {
          const card = createKanbanCard(item, type);
          column.appendChild(card);
        });
      }

      // Populando colunas


      populateColumn('agendamentos', data.agendamentos, 'agendamentos');
      populateColumn('novosPacientes', data.novosPacientes, 'novosPacientes');
      populateColumn('novosMedicos', data.novosMedicos, 'novosMedicos');
      populateColumn('acompanhamentosMedicos', data.acompanhamentosMedicos, 'acompanhamentosMedicos');

      $('.column').each(function () {
        const columnCount = $(this).find('.column-count');
        const cardCount = $(this).find('.kanban-card').length;

        $(columnCount).text(cardCount + ' registro(s)');
      });
    })
    .catch(error => console.error('Erro ao carregar os dados:', error));
});