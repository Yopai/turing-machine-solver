import {Http} from "./http.js";

class Storage {
    /** @var {string} */
    #hash;

    constructor(hash) {
        this.#hash = hash;
        this.loading = false;
    }

    load() {
        if (typeof (localStorage[this.#hash]) === 'undefined') {
            return;
        }
        this.loading = true;
        const data = JSON.parse(localStorage[hash]);
        let prevtr = null;
        data.tries.forEach((tryData) => {
            if (tryData.code.trim === '') {
                return;
            }
            const tr = ui.addTryRow();
            const inputs = tr.querySelectorAll('input');
            inputs.forEach((input, k) => {
                input.value = tryData.code[k];
            });
            const answers = tr.querySelectorAll('td');
            answers.forEach((answer, k) => {
                if (tryData.answers[k] !== null) {
                    ui._set_answer(answer, tryData.answers[k] === 'true');
                }
            });

            if (prevtr) {
                ui.lockPreviousTry(prevtr);
            }
            prevtr = tr;
        });

        Object.keys(data.criteria).forEach((key) => {
            const selector =
                '[data-id="' + key.slice(0, -1) + '"] '
                + '[data-letter="' + key.slice(-1) + '"] '
                + (data.criteria[key] ? '.correct' : '.exclude');
            ui.toggleDisabled(document.querySelector(selector))
        })

        data.excludedDigits.forEach((sd) => {
            ui.strikeDigit(sd.symbol, sd.digit);
        });

        data.excludedCodes.forEach((code) => {
            ui.strikeCode(code, true);
        });
        this.loading = false;
    }

    save() {
        if (this.loading) {
            return;
        }
        const ls = {
            tries: [],
            criteria: {},
            excludedDigits: [],
            excludedCodes: []
        };
        document.querySelectorAll('.tries tbody tr').forEach((tr) => {
            const tryData = {
                code: ui.getCode(tr),
                answers: []
            };
            console.log(tryData.code.trim());
            if (ui.getCode(tr).trim() === '') {
                return;
            }
            tr.querySelectorAll('body td').forEach((td) => {
                tryData.answers.push(td.dataset.answer);
            })
            if (tryData.code.trim().length === 3) {
                ls.tries.push(tryData);
            }
        });

        document.querySelectorAll('.exclude.active, .correct.active').forEach((btn) => {
            const key = btn.parentElement.parentElement.dataset.id + btn.parentElement.dataset.letter;
            ls.criteria[key] = btn.classList.contains('correct');
        })

        document.querySelectorAll('.possible-digits td.fullstrike').forEach((td) => {
            const [ignored, symbol, digit] = td.id.split('-');
            ls.excludedDigits.push({symbol: symbol, digit: digit});
        });
        document.querySelectorAll('.possible-codes div.fullstrike').forEach((div) => {
            ls.excludedCodes.push(div.id.replace('pcode-', ''));
        })
        localStorage[this.#hash] = JSON.stringify(ls);
    }
}

class Problem {
    _show_hints(code) {
        ui.hideHints();
        Http.api('hint/' + encodeURIComponent(hash) + '/' + encodeURIComponent(code))
            .then((json) => {
                const selector = '.row.total .criterion';
                document.querySelector(selector).innerHTML = 'Total '+code;
                json.answers.forEach((answer) => {
                    const criterion = document.querySelector('.row[data-id="' + answer.id + '"] .criterion');
                    const hintselector = '.hint' + (answer.answer ? '.yes' : '.no');
                    const e = criterion.querySelector(hintselector);
                    if (e) {
                        e.classList.remove('hide');
                    }

                    let cell = criterion.nextElementSibling;
                    while (cell) {
                        const selector = '.row.total [data-letter="' + cell.dataset.letter + '"] ' + hintselector;
                        const e = document.querySelector(selector)
                        if (e.innerHTML === '') {
                            e.innerHTML = '0';
                        }
                        if (!cell.classList.contains('fullstrike')) {
                            e.innerHTML++;
                        }
                        e.classList.remove('hide');
                        cell = cell.nextElementSibling;
                    }
                })
            });
    }
}

class UI {
    #storage;

    constructor(storage) {
        this.#storage = storage;
    }

    addTryRow() {
        const template = document.getElementById('try-row');
        const tbody = template.parentElement;
        const clone = template.content.cloneNode(true);
        const id = 'try' + template.dataset.nextIdValue;
        clone.querySelectorAll('tr')[0].id = id;
        template.dataset.nextIdValue++;
        tbody.appendChild(clone);

        const tr = document.getElementById(id);

        const allInput = $('#' + id + ' input');
        allInput.on('keydown', function (event) {
            if (event.key >= '1' && event.key <= '5') {
                event.target.value = event.key;
                ui.hideHints();
                const nextinp = event.target.parentElement.nextElementSibling.getElementsByTagName('input').item(0);
                if (nextinp) {
                    nextinp.focus();
                }
            } else if (event.key === 'Enter') {
                ui.showHints(document.querySelector('#' + id + ' th button'));
            } else if (event.key === 'Backspace') {
                if (event.target.value) {
                    event.target.value = '';
                } else {
                    let previnp = event.target.parentElement.previousElementSibling;
                    if (previnp) {
                        previnp = previnp.getElementsByTagName('input').item(0);
                        previnp.value = '';
                        previnp.focus();
                    }
                }
                ui.hideHints();
            } else if (event.key >= 'A' && event.key <= 'Z') {
                tr.querySelector('.try[data-letter="' + event.key + '"]').click();
            }
            event.stopPropagation();
            event.preventDefault();
        });
        $('#' + id + ' .try').on('click', (event) => {
            ui.tryCode(event.target, event.target.dataset.letter);
        })
        $('#' + id + ' [data-click="hint"]').on('click', (event) => {
            ui.showHints(event.target);
        });

        return tr;
    }

    /**
     * @param {HTMLElement} td
     * @param {boolean} answer
     */
    setAnswer(td, answer) {
        ui._set_answer(td, answer);
    }

    _set_answer(td, answer) {
        td.innerHTML = (answer
                ? '<span class="yes"/>'
                : '<span class="no"/>'
        )
        td.dataset.answer = answer;
        const tr = td.parentElement;
        if (tr.previousElementSibling) {
            ui.lockPreviousTry(tr.previousElementSibling);
        }
    }

    showHints(btn) {
        problem._show_hints(ui.getCode(btn.parentElement.parentElement));
    }

    getCode(tr) {
        let result = '';
        tr.querySelectorAll('input').forEach((input) => {
            result += input.value ? input.value : ' ';
        });
        return result;
    }

    toggleDisabled(button) {
        button.classList.toggle('active');
        const td = button.parentElement;
        td.classList.add('fullstrike');

        const tr = td.parentElement;
        if (tr.querySelectorAll('div.cell:not(.fullstrike)').length === 0) {
            tr.classList.toggle('fullstrike', button.classList.contains('active'));
        }
        storage.save();
        return false;
    }

    tryCode(btn, letter) {
        const tr = btn.parentElement.parentElement;
        const code = ui.getCode(tr);

        if (code.includes(' ')) {
            // ignore
            return;
        }

        Http.api('try/' + encodeURIComponent(hash) + '/' + code + '/' + letter)
            .then((json) => {
                ui._set_answer(btn.parentElement, json.answer);
                console.log(btn, btn.parentElement.parentElement);
                if (btn.parentElement.parentElement.nextElementSibling === null) {
                    ui.addTryRow();
                }
                storage.save();
            });
    }

    lockPreviousTry(prevtr) {
        prevtr.classList.add('locked');
        prevtr.querySelectorAll('input').forEach((input) => {
            input.disabled = true;
        });
    }

    checkCode() {
        const code = document.querySelector('.possible-codes div:not(.fullstrike)').id.replace('pcode-', '');
        Http.api('check/' + encodeURIComponent(hash) + '/' + code)
            .then((json) => {
                if (json) {
                    if (confirm('Le code est le bon, féliciations !. Nouvelle partie ?')) {
                        ui.newGame();
                    }
                } else {
                    alert("Dommage, ce n'est pas le bon code...");
                }
            });
    }

    hideHints() {
        document.querySelectorAll('.hint').forEach((span) => {
            span.classList.add('hide');
        });

        document.querySelectorAll('.total .hint').forEach((span) => {
            span.innerHTML = '';
        });
    }

    resetSheet() {
        forAll('.fullstrike', (e) => e.classList.remove('fullstrike', 'hide'));
        forAll('.exclude.active, .correct.active', (e) => e.classList.remove('active'));
        document.getElementById('btn-check').classList.add('hide');
        // delete(localStorage[hash]);
        storage.save();
    }

    newGame() {
        document.location = '/';
    }

    setDigit(symbol, value) {
        for (let i = 1; i <= 5; i++) {
            const e = document.getElementById('digit-' + symbol + '-' + i);
            if (i != value) {
                e.classList.add('fullstrike');
                ui.filterCodes(symbol, i);
            }
        }
        storage.save();
    }

    strikeDigit(symbol, value) {
        const e = document.getElementById('digit-' + symbol + '-' + value);
        e.classList.add('fullstrike');
        ui.filterCodes(symbol, value);
        storage.save();
    }

    strikeCode(code, hide = true) {
        const classes = document.getElementById('pcode-' + code).classList;
        classes.add('fullstrike');
        if (hide) {
            classes.add('hide');
        }
        if (document.querySelectorAll('.possible-codes div:not(.fullstrike)').length === 1) {
            document.getElementById('btn-check').classList.remove('hide');
        }
        storage.save();
    }

    filterCodes(symbol, value) {
        forAll('.possible-codes [data-' + symbol + '="' + value + '"]', (e) => {
            const code = e.dataset.triangle + e.dataset.square + e.dataset.circle;
            ui.strikeCode(code);
        })
    }

    cleanCodes() {
        forAll('.possible-codes .fullstrike', (e) => e.classList.add('hide'));
    }

    addHandlers() {
        $('[data-click="strike"]').on('click', (event) => {
            ui.toggleDisabled(event.target);
        });
        $('.digit .set-yes').on('click', (event) => {
            const [ignored, symbol, digit] = event.target.parentElement.id.split('-');
            ui.setDigit(symbol, digit);
        })
        $('.digit .set-no').on('click', (event) => {
            const [ignored, symbol, digit] = event.target.parentElement.id.split('-');
            ui.strikeDigit(symbol, digit);
        })
        $('.possible-codes a').on('click', (event) => {
            problem._show_hints(event.target.innerHTML);
            event.stopPropagation();
            event.preventDefault();
        })
        $('.possible-codes [data-code]').on('click', (event) => {
            ui.strikeCode(event.target.dataset.code, false);
        });
        $('[data-answer]').on('click', (event) => {
            problem.setAnswer(event.target.parentElement, event.target.dataset.answer === 'yes');
        })
        $('#btn-reset').on('click', (event) => {
            ui.resetSheet();
        });
        $('#btn-reset-tries').on('click', (event) => {
            ui.resetSheet();
            ui.resetTries();
        });
        $('#btn-new').on('click', (event) => {
            ui.newGame();
        });
        $('#btn-clean').on('click', (event) => {
            ui.cleanCodes();
        });
        $('#btn-check').on('click', (event) => {
            ui.checkCode();
        });
    }

    resetTries() {
        const template = document.getElementById('try-row');
        const tbody = template.parentElement;
        let tr = template.nextElementSibling;
        while (tr) {
            tbody.removeChild(tr);
            tr = template.nextElementSibling;
        }
        storage.save();
        ui.addTryRow();
    }
}

const problem = new Problem();
const storage = new Storage(hash);
const ui = new UI();

function forAll(selectors, callback) {
    document.querySelectorAll(selectors).forEach(callback);
}

document.addEventListener('DOMContentLoaded', (event) => {
    storage.load();
    ui.addTryRow();
    ui.addHandlers()
});
