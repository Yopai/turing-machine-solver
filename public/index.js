

function toggle_card(n, shown) {
  let e = document.getElementById('card_' + n + 'display');
  e.classList.toggle('hide', !shown);
}

function show_solutions(result) {
  let wait = document.getElementById('solutions-wait');
  let done = document.getElementById('solutions-done');
  wait.classList.toggle('hide', result !== false);
  done.classList.toggle('hide', result === false);
  if (result !== false) {
    let tpl = document.getElementById('solution-template');
    let esol= document.getElementById('solutions');
    let text = (result.count ? result.count : 'No') + ' solution'+(result.count > 1 ? 's' : '');
    done.querySelector('[data-model="count"]').innerHTML = text;
    result.solutions.forEach(function(solution) {
      let df = tpl.content.cloneNode(true);
      let e = df.children[0];
      e.querySelector('[data-model="code"]').innerHTML = solution.code;
      let ul = e.querySelector('[data-model="criteria[]"]');
      solution.criteria.forEach(function(criterion) {
        let li = document.createElement('li');
        li.innerHTML = criterion;
        ul.appendChild(li);
      });
      esol.appendChild(df);
    });
  }
}

function solve(form) {
  show_solutions(false);
  fetch('/api/solve', {
    method: "POST",
    contentType: 'multipart/form-data',
    body: new FormData(form)
  })
          .then(function (response) {
            return response.json();
          })
          .then(function (jsonResponse) {
            show_solutions(jsonResponse);
          });
}

