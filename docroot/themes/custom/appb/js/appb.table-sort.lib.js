class TableSort {

  /**
   * @type {HTMLTableElement}
   */
  tableElement;

  /**
   * @type {string}
   */
  ascendingSymbol = '&#9650;';

  /**
   * @type {string}
   */
  descendingSymbol = '&#9660;';

  /**
   * @param {HTMLTableElement} tableElement
   */
  constructor(tableElement) {
    this.tableElement = tableElement;

    this.initOriginalRowIndexes();
    this.initMarkup();
    this.initEventHandlers();
  }

  initOriginalRowIndexes() {
    const rows = this.tableElement.querySelectorAll(':scope > tbody > tr');
    for (let i = 0; i < rows.length; i++) {
      rows[i].setAttribute('data-table-sort-original-index', i);
    }
  }

  initMarkup() {
    const headerCells = this.tableElement.querySelectorAll(':scope > thead > tr > th.table-sort--sortable');
    for (let i = 0; i < headerCells.length; i++) {
      this.initMarkupHeaderCell(headerCells[i]);
    }
  }

  /**
   * @param {HTMLTableCellElement} headerCell
   */
  initMarkupHeaderCell(headerCell) {
    const wrapperElement = document.createElement('span');
    wrapperElement
      .classList
      .add(
        'table-sort--wrapper',
        'state-inactive',
      )

    const buttonElement = document.createElement('button');
    buttonElement
      .classList
      .add(
        'table-sort--trigger',
      );

    const positionElement = document.createElement('span');
    positionElement
      .classList
      .add(
        'table-sort--position',
      );
    positionElement.innerHTML = '&nbsp;'

    let attr = 'data-table-sort-default-dir';
    const dirElement = document.createElement('span');
    dirElement
      .classList
      .add(
        'table-sort--dir',
      );
    dirElement.innerHTML = !headerCell.hasAttribute(attr) || headerCell.getAttribute(attr) === 'asc' ?
      this.ascendingSymbol
      : this.descendingSymbol;

    buttonElement.append(positionElement, dirElement);
    wrapperElement.append(buttonElement);
    headerCell.append(wrapperElement);
  }

  initEventHandlers() {
    const triggers = this.tableElement.querySelectorAll(':scope > thead > tr > th .table-sort--trigger');
    let eventListener = this.getOnTriggerClickEventListener();
    for (let i = 0; i < triggers.length; i++) {
      triggers[i].addEventListener('click', eventListener);
    }
  }

  /**
   * @return {EventListener}
   */
  getOnTriggerClickEventListener() {
    const self = this;

    /**
     * @param {Event} event
     */
    return function (event) {
      const headerCell = event.currentTarget.closest('th');
      const targetDir = self.getTargetDir(headerCell);

      if (targetDir) {
        headerCell.setAttribute('data-table-sort-dir', targetDir);
      }
      else {
        headerCell.removeAttribute('data-table-sort-dir');
      }

      self.updateDir(headerCell);
      self.clearOutdatedPositions();
      self.updatePositions();

      self.doSort();
    };
  }

  /**
   * @param {HTMLTableCellElement} headerCell
   *
   * @return {string}
   */
  getTargetDir(headerCell) {
    const defaultDir = headerCell.hasAttribute('data-table-sort-default-dir') ?
      headerCell.getAttribute('data-table-sort-default-dir')
      : 'asc';

    let currentDir = headerCell.hasAttribute('data-table-sort-dir') ?
      headerCell.getAttribute('data-table-sort-dir')
      : '';

    const dirs = [
      defaultDir,
      defaultDir === 'asc' ? 'desc' : 'asc',
      '',
    ];

    let currentIndex = dirs.indexOf(currentDir);

    return currentIndex + 1 < dirs.length ? dirs[currentIndex + 1] : dirs[0];
  }

  /**
   * @param {HTMLTableCellElement} headerCell
   */
  updateDir(headerCell) {
    const defaultDir = headerCell.getAttribute('data-table-default-dir') || 'asc';
    const currentDir = headerCell.getAttribute('data-table-sort-dir')

    console.log('currentDir = ' + currentDir);
    console.log('defaultDir = ' + defaultDir);

    const wrapper = headerCell.querySelector(':scope > .table-sort--wrapper');
    wrapper.classList.toggle('state-inactive', !currentDir);

    const dirElement = wrapper.querySelector('.table-sort--dir');
    const dir = currentDir || defaultDir;
    dirElement.innerHTML = dir === 'asc' ? this.ascendingSymbol : this.descendingSymbol;
  }

  clearOutdatedPositions() {
    const headerCells = this.tableElement.querySelectorAll('thead > tr > th:not([data-table-sort-dir]).table-sort--sortable');
    for (let i = 0; i < headerCells.length; i++) {
      this.updatePosition(headerCells[i], 0);
    }
  }

  updatePositions() {
    let headerCells = this.tableElement.querySelectorAll('thead > tr > th[data-table-sort-dir]')
    let numOfNew = this
      .tableElement
      .querySelectorAll('thead > tr > th[data-table-sort-dir]:not([data-table-sort-position])')
      .length;

    let oldNumOfCells = 0;
    let position = 0;
    for (let i = 0; i < headerCells.length; i++) {
      position = headerCells[i].getAttribute('data-table-sort-position') || '0';
      position = parseInt(position);
      if (position > oldNumOfCells) {
        oldNumOfCells = position;
      }
    }

    const diff = oldNumOfCells - headerCells.length;
    for (let i = 0; i < headerCells.length; i++) {
      position = headerCells[i].getAttribute('data-table-sort-position') || '0';
      position = parseInt(position);
      if (position === 0) {
        position = headerCells.length - --numOfNew;
      }
      else if (diff > 0 && position >= headerCells.length) {
        position = position - diff;
      }

      this.updatePosition(headerCells[i], position);
    }
  }

  /**
   * @param {HTMLTableCellElement} headerCell
   * @param {number} position
   */
  updatePosition(headerCell, position) {
    if (!position) {
      headerCell.removeAttribute('data-table-sort-position');
    }
    else {
      headerCell.setAttribute('data-table-sort-position', position.toString());
    }

    let positionElement = headerCell.querySelector('.table-sort--position');
    if (positionElement) {
      positionElement.innerHTML = position ? position.toString() : '&nbsp';
    }
  }

  doSort() {
    /** @var {HTMLTableRowElement[]} rows */
    let rows = Array.from(this.tableElement.querySelectorAll('tbody > tr'));
    rows.sort(this.getRowComparer());
    for (let i = 0; i < rows.length; i++) {
      rows[i].parentElement.appendChild(rows[i]);
    }
  }

  getRowComparer() {
    /** @var {HTMLTableCellElement[]} headerCells */
    const headerCells = Array.from(this.tableElement.querySelectorAll('thead > tr > th[data-table-sort-position]'));

    const originalIndexComparer = this.getElementComparerByNumericAttribute('data-table-sort-original-index');
    if (headerCells.length === 0) {
      return originalIndexComparer;
    }

    headerCells.sort(this.getElementComparerByNumericAttribute('data-table-sort-position'));

    /**
     * @param {HTMLTableRowElement} a
     * @param {HTMLTableRowElement} b
     *
     * @return {number}
     */
    return function (a, b) {
      let result = 0;
      let cellIndex;
      let aContent;
      let bContent;
      for (let i = 0; i < headerCells.length; i++) {
        cellIndex = headerCells[i].cellIndex;
        aContent = a.cells[cellIndex].textContent;
        bContent = b.cells[cellIndex].textContent;
        result = aContent.localeCompare(bContent);
        if (!result) {
          continue;
        }

        let dir = headerCells[i].getAttribute('data-table-sort-dir');
        if (dir === 'desc') {
          result *= -1;
        }

        break;
      }

      return result || originalIndexComparer(a, b);
    };
  }

  /**
   * @param {string} name
   *
   * @return {function(HTMLElement, HTMLElement): number}
   */
  getElementComparerByNumericAttribute(name) {
    /**
     * @param {HTMLElement} a
     * @param {HTMLElement} b
     *
     * @return {number}
     */
    return function (a, b) {
      const aNum = parseInt(a.getAttribute(name) || '0');
      const bNum = parseInt(b.getAttribute(name) || '0');

      return aNum === bNum ? 0 : (aNum < bNum ? -1 : 1);
    };
  }

}
