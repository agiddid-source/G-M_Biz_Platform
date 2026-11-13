/*
 * GENERATOR-MS shared state helper (SHARED FILE: change by agreement of all members).
 *
 * Seeds from data/M1|M2|M3/*.json, keeps changes in localStorage (browser-only, simulated).
 * Nothing here is written back to the JSON files or to any server.
 * Requires window.GMS_BASE (set in _inc/config.php), e.g. "/" or "/generator-ms/".
 */
(function (global) {
  'use strict';

  var NS = 'gms:';
  // Bump when any seed file changes shape: wipes local changes on next load.
  var SEED_VERSION = '2026-11-12.1';

  var COLLECTIONS = {
    'products':         { idField: 'product_id',   prefix: 'PRD' },
    'categories':       { idField: 'category_id',  prefix: 'CAT' },
    'brands':           { idField: 'brand_id',     prefix: 'BRD' },
    'customers':        { idField: 'customer_id',  prefix: 'CUS' },
    'sales':            { idField: 'sale_id',      prefix: 'SAL' },
    'rentals':          { idField: 'rental_id',    prefix: 'RNT' },
    'rental-enquiries': { idField: 'enquiry_id',   prefix: 'ENQ' },
    'workers':          { idField: 'worker_id',    prefix: 'WRK' },
    'equipment':        { idField: 'equipment_id', prefix: 'EQP' }
  };
  var OBJECTS = ['settings']; // single-object datasets

  // Seed files sit in one folder per member: data/M1, data/M2, data/M3 (see docs/DATA-DICTIONARY.md).
  var SEED_DIR = {
    'products': 'M1', 'categories': 'M1', 'brands': 'M1',
    'customers': 'M2', 'sales': 'M2',
    'rentals': 'M3', 'rental-enquiries': 'M3', 'workers': 'M3', 'equipment': 'M3', 'settings': 'M3'
  };
  function seedUrl(name) {
    var dir = SEED_DIR[name];
    return base() + 'data/' + (dir ? dir + '/' : '') + name + '.json';
  }

  var cache = {};

  function base() { return global.GMS_BASE || ''; }

  function lsGet(key) {
    try { return global.localStorage.getItem(key); } catch (e) { return null; }
  }
  function lsSet(key, value) {
    try { global.localStorage.setItem(key, value); } catch (e) { /* storage full or blocked: stay in memory */ }
  }
  function lsRemove(key) {
    try { global.localStorage.removeItem(key); } catch (e) { /* ignore */ }
  }

  function emit(name, action, id) {
    global.document.dispatchEvent(new global.CustomEvent('gms:change', {
      detail: { name: name, action: action, id: id || null }
    }));
  }

  function persist(name) {
    lsSet(NS + name, JSON.stringify(cache[name]));
  }

  function checkSeedVersion() {
    if (lsGet(NS + 'seed_version') === SEED_VERSION) return;
    Object.keys(COLLECTIONS).concat(OBJECTS).forEach(function (n) { lsRemove(NS + n); });
    lsSet(NS + 'seed_version', SEED_VERSION);
  }

  function meta(name) {
    var m = COLLECTIONS[name];
    if (!m) throw new Error('Store: "' + name + '" is not a registered collection.');
    return m;
  }

  function loaded(name) {
    if (cache[name] === undefined) {
      throw new Error('Store: "' + name + '" is not loaded. Call Store.ready("' + name + '") first.');
    }
    return cache[name];
  }

  function load(name) {
    if (cache[name] !== undefined) return Promise.resolve(cache[name]);
    checkSeedVersion();
    var raw = lsGet(NS + name);
    if (raw) {
      try {
        cache[name] = JSON.parse(raw);
        return Promise.resolve(cache[name]);
      } catch (e) { /* corrupted: fall back to seed */ }
    }
    return global.fetch(seedUrl(name)).then(function (res) {
      if (!res.ok) throw new Error('Store: could not load ' + seedUrl(name) + ' (' + res.status + ')');
      return res.json();
    }).then(function (data) {
      cache[name] = data;
      persist(name);
      return data;
    });
  }

  var Store = {
    SEED_VERSION: SEED_VERSION,

    /** Load one or more datasets. Resolves when all are available. */
    ready: function () {
      var names = Array.prototype.slice.call(arguments);
      return Promise.all(names.map(load));
    },

    all: function (name) { meta(name); return loaded(name); },

    find: function (name, id) {
      var f = meta(name).idField;
      return loaded(name).find(function (r) { return r[f] === id; }) || null;
    },

    where: function (name, predicate) {
      meta(name);
      return loaded(name).filter(predicate);
    },

    nextId: function (name) {
      var m = meta(name), max = 0;
      loaded(name).forEach(function (r) {
        var n = parseInt(String(r[m.idField]).split('-')[1], 10);
        if (n > max) max = n;
      });
      var num = String(max + 1);
      while (num.length < 3) num = '0' + num;
      return m.prefix + '-' + num;
    },

    /** Adds a record. The ID is assigned here unless the record already has one. */
    add: function (name, record) {
      var m = meta(name);
      var rec = Object.assign({}, record);
      if (!rec[m.idField]) rec[m.idField] = Store.nextId(name);
      loaded(name).push(rec);
      persist(name);
      emit(name, 'add', rec[m.idField]);
      return rec;
    },

    /** Shallow-merges patch into the record. Returns the updated record or null. */
    update: function (name, id, patch) {
      var rec = Store.find(name, id);
      if (!rec) return null;
      Object.assign(rec, patch);
      persist(name);
      emit(name, 'update', id);
      return rec;
    },

    remove: function (name, id) {
      var f = meta(name).idField, list = loaded(name);
      var i = list.findIndex(function (r) { return r[f] === id; });
      if (i === -1) return false;
      list.splice(i, 1);
      persist(name);
      emit(name, 'remove', id);
      return true;
    },

    /** Single-object datasets (settings). */
    getObject: function (name) {
      if (OBJECTS.indexOf(name) === -1) throw new Error('Store: "' + name + '" is not an object dataset.');
      return loaded(name);
    },
    saveObject: function (name, obj) {
      if (OBJECTS.indexOf(name) === -1) throw new Error('Store: "' + name + '" is not an object dataset.');
      cache[name] = obj;
      persist(name);
      emit(name, 'update', null);
    },

    /** Restore seed data. Pass a name to reset one dataset, or nothing for all. Reloads the page. */
    reset: function (name) {
      var names = name ? [name] : Object.keys(COLLECTIONS).concat(OBJECTS);
      names.forEach(function (n) { lsRemove(NS + n); delete cache[n]; });
      if (!name) lsRemove(NS + 'seed_version');
      global.location.reload();
    },

    /** "Today" for the prototype. Uses settings.prototype.demo_today when set so seed dates stay coherent. */
    today: function () {
      var s = cache.settings;
      var demo = s && s.prototype && s.prototype.demo_today;
      return demo || new Date().toISOString().slice(0, 10);
    }
  };

  // Keep other open tabs in sync.
  global.addEventListener('storage', function (e) {
    if (!e.key || e.key.indexOf(NS) !== 0) return;
    var name = e.key.slice(NS.length);
    if (name === 'seed_version' || cache[name] === undefined) return;
    try {
      cache[name] = e.newValue ? JSON.parse(e.newValue) : cache[name];
      emit(name, 'sync');
    } catch (err) { /* ignore */ }
  });

  global.Store = Store;
})(window);