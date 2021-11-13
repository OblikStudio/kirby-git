var __defProp = Object.defineProperty;
var __defProps = Object.defineProperties;
var __getOwnPropDescs = Object.getOwnPropertyDescriptors;
var __getOwnPropSymbols = Object.getOwnPropertySymbols;
var __hasOwnProp = Object.prototype.hasOwnProperty;
var __propIsEnum = Object.prototype.propertyIsEnumerable;
var __defNormalProp = (obj, key, value) => key in obj ? __defProp(obj, key, { enumerable: true, configurable: true, writable: true, value }) : obj[key] = value;
var __spreadValues = (a, b) => {
  for (var prop in b || (b = {}))
    if (__hasOwnProp.call(b, prop))
      __defNormalProp(a, prop, b[prop]);
  if (__getOwnPropSymbols)
    for (var prop of __getOwnPropSymbols(b)) {
      if (__propIsEnum.call(b, prop))
        __defNormalProp(a, prop, b[prop]);
    }
  return a;
};
var __spreadProps = (a, b) => __defProps(a, __getOwnPropDescs(b));
(function() {
  "use strict";
  var render$3 = function() {
    var _vm = this;
    var _h = _vm.$createElement;
    var _c = _vm._self._c || _h;
    return _c("section", [_c("header", { staticClass: "k-section-header" }, [_c("k-headline", [_vm._v(_vm._s(_vm.finalHeadline))]), _vm.list.length ? _c("k-button-group", [_c("k-button", { attrs: { "icon": "share", "link": _vm.link } }, [_vm._v("Open")])], 1) : _vm._e()], 1), _vm.list.length ? _c("k-list", _vm._l(_vm.list, function(entry) {
      return _c("k-list-item", { key: entry.icon, attrs: { "text": entry.text, "icon": { type: entry.icon, class: "git-icon-change" }, "image": true } });
    }), 1) : [_c("k-empty", { attrs: { "icon": "check" } }, [_vm._v("No changes")])]], 2);
  };
  var staticRenderFns$3 = [];
  render$3._withStripped = true;
  function normalizeComponent(scriptExports, render2, staticRenderFns2, functionalTemplate, injectStyles, scopeId, moduleIdentifier, shadowMode) {
    var options = typeof scriptExports === "function" ? scriptExports.options : scriptExports;
    if (render2) {
      options.render = render2;
      options.staticRenderFns = staticRenderFns2;
      options._compiled = true;
    }
    if (functionalTemplate) {
      options.functional = true;
    }
    if (scopeId) {
      options._scopeId = "data-v-" + scopeId;
    }
    var hook;
    if (moduleIdentifier) {
      hook = function(context) {
        context = context || this.$vnode && this.$vnode.ssrContext || this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext;
        if (!context && typeof __VUE_SSR_CONTEXT__ !== "undefined") {
          context = __VUE_SSR_CONTEXT__;
        }
        if (injectStyles) {
          injectStyles.call(this, context);
        }
        if (context && context._registeredComponents) {
          context._registeredComponents.add(moduleIdentifier);
        }
      };
      options._ssrRegister = hook;
    } else if (injectStyles) {
      hook = shadowMode ? function() {
        injectStyles.call(this, (options.functional ? this.parent : this).$root.$options.shadowRoot);
      } : injectStyles;
    }
    if (hook) {
      if (options.functional) {
        options._injectStyles = hook;
        var originalRender = options.render;
        options.render = function renderWithStyleInjection(h, context) {
          hook.call(context);
          return originalRender(h, context);
        };
      } else {
        var existing = options.beforeCreate;
        options.beforeCreate = existing ? [].concat(existing, hook) : [hook];
      }
    }
    return {
      exports: scriptExports,
      options
    };
  }
  let updateEvents = [
    "site.changeTitle",
    "page.changeTitle",
    "page.changeStatus",
    "model.update"
  ];
  function getStats() {
    return {
      total: 0,
      added: 0,
      untracked: 0,
      modified: 0,
      renamed: 0,
      deleted: 0
    };
  }
  const __vue2_script$3 = {
    data: () => {
      return {
        headline: null,
        stats: getStats()
      };
    },
    computed: {
      finalHeadline() {
        let text = this.headline;
        if (this.stats.total) {
          text += ` (${this.stats.total} changes)`;
        }
        return text;
      },
      link() {
        return window.panel.$url("git").toString();
      },
      positiveStatus() {
        let text = [];
        if (this.stats.added) {
          text.push(`${this.stats.added} added`);
        }
        if (this.stats.untracked) {
          text.push(`${this.stats.untracked} untracked`);
        }
        if (text.length) {
          return text.join(", ");
        } else {
          return null;
        }
      },
      noticeStatus() {
        let text = [];
        if (this.stats.modified) {
          text.push(`${this.stats.modified} modified`);
        }
        if (this.stats.renamed) {
          text.push(`${this.stats.renamed} renamed`);
        }
        if (text.length) {
          return text.join(", ");
        } else {
          return null;
        }
      },
      negativeStatus() {
        if (this.stats.deleted) {
          return `${this.stats.deleted} deleted`;
        } else {
          return null;
        }
      },
      list() {
        let result = [];
        if (this.negativeStatus) {
          result.push({ icon: "trash", text: this.negativeStatus });
        }
        if (this.noticeStatus) {
          result.push({ icon: "edit", text: this.noticeStatus });
        }
        if (this.positiveStatus) {
          result.push({ icon: "copy", text: this.positiveStatus });
        }
        return result;
      }
    },
    created() {
      this.load().then((response) => {
        this.headline = response.headline;
        this.status();
      });
      this.$events.$on(updateEvents, this.status);
    },
    destroyed() {
      this.$events.$off(updateEvents, this.status);
    },
    methods: {
      status() {
        this.$api.get("git/status").then((entries) => {
          this.stats = getStats();
          if (entries.length) {
            this.updateStats(entries);
          }
        });
      },
      updateStats(entries) {
        this.stats.total = entries.length;
        entries.forEach((entry) => {
          let status = entry.staged || entry.unstaged;
          switch (status) {
            case "A":
              this.stats.added++;
              break;
            case "?":
              this.stats.untracked++;
              break;
            case "M":
              this.stats.modified++;
              break;
            case "R":
              this.stats.renamed++;
              break;
            case "D":
              this.stats.deleted++;
          }
        });
      }
    }
  };
  const __cssModules$3 = {};
  var __component__$3 = /* @__PURE__ */ normalizeComponent(__vue2_script$3, render$3, staticRenderFns$3, false, __vue2_injectStyles$3, null, null, null);
  function __vue2_injectStyles$3(context) {
    for (let o in __cssModules$3) {
      this[o] = __cssModules$3[o];
    }
  }
  __component__$3.options.__file = "src/components/GitSection.vue";
  var GitSection = /* @__PURE__ */ function() {
    return __component__$3.exports;
  }();
  var render$2 = function() {
    var _vm = this;
    var _h = _vm.$createElement;
    var _c = _vm._self._c || _h;
    return _c("section", { staticClass: "area-git-changes-list" }, [_c("header", { staticClass: "k-section-header" }, [_c("k-headline", [_vm._v(_vm._s(_vm.title))]), _vm._t("action")], 2), _vm.entries ? _c("k-items", { attrs: { "layout": _vm.list } }, _vm._l(_vm.entries, function(entry) {
      return _c("k-item", { key: entry.file, attrs: { "text": entry.file, "image": { back: "none", icon: entry.icon }, "layout": _vm.list } });
    }), 1) : [_c("k-empty", { attrs: { "icon": "check" } }, [_vm._v("No changes")])], _c("k-pagination", { attrs: { "align": "center", "details": true, "page": _vm.pageIdx + 1, "total": _vm.data.length, "limit": _vm.perPage }, on: { "paginate": _vm.changePage } })], 2);
  };
  var staticRenderFns$2 = [];
  render$2._withStripped = true;
  var ChangesList_vue_vue_type_style_index_0_lang = "";
  const __vue2_script$2 = {
    props: {
      title: {
        type: String
      },
      data: {
        type: Array
      }
    },
    data() {
      return {
        perPage: 15,
        pageIdx: 0
      };
    },
    computed: {
      pages() {
        return this.data.reduce((acc, val, i) => {
          let idx = Math.floor(i / this.perPage);
          let page = acc[idx] || (acc[idx] = []);
          page.push(val);
          return acc;
        }, []);
      },
      page() {
        if (!this.pages[this.pageIdx]) {
          this.pageIdx = 0;
        }
        return this.pages[this.pageIdx];
      },
      entries() {
        if (this.page) {
          return this.page.map((entry) => {
            return {
              file: entry.file,
              mode: entry.mode,
              icon: this.getIcon(entry.mode)
            };
          });
        } else {
          return null;
        }
      }
    },
    methods: {
      changePage(data) {
        this.pageIdx = data.page - 1;
      },
      getIcon(mode) {
        let icon = "dots";
        switch (mode) {
          case "?":
          case "A":
            icon = "copy";
            break;
          case "M":
            icon = "edit";
            break;
          case "R":
            icon = "refresh";
            break;
          case "D":
            icon = "trash";
        }
        return icon;
      }
    }
  };
  const __cssModules$2 = {};
  var __component__$2 = /* @__PURE__ */ normalizeComponent(__vue2_script$2, render$2, staticRenderFns$2, false, __vue2_injectStyles$2, null, null, null);
  function __vue2_injectStyles$2(context) {
    for (let o in __cssModules$2) {
      this[o] = __cssModules$2[o];
    }
  }
  __component__$2.options.__file = "src/components/ChangesList.vue";
  var ChangesList = /* @__PURE__ */ function() {
    return __component__$2.exports;
  }();
  var render$1 = function() {
    var _vm = this;
    var _h = _vm.$createElement;
    var _c = _vm._self._c || _h;
    return _c("section", [_c("header", { staticClass: "k-section-header" }, [_c("k-headline", [_vm._v("Commits")]), _vm._t("action")], 2), _vm.data && _vm.commits.length ? _c("k-list", _vm._l(_vm.commits, function(commit) {
      return _c("k-item", { key: commit.hash, attrs: { "text": commit.subject, "info": commit.hash, "image": true } });
    }), 1) : [_c("k-empty", { attrs: { "icon": "circle-filled" } }, [_vm._v("No commits")])], _vm.data ? _c("k-pagination", _vm._g({ attrs: { "align": "center", "details": true, "page": _vm.page, "total": _vm.data.total, "limit": _vm.limit } }, _vm.$listeners)) : _vm._e()], 2);
  };
  var staticRenderFns$1 = [];
  render$1._withStripped = true;
  var CommitsList_vue_vue_type_style_index_0_lang = "";
  const __vue2_script$1 = {
    props: {
      data: Object
    },
    data() {
      return {
        page: 1,
        limit: 15
      };
    },
    computed: {
      commits() {
        return this.data.commits.map((commit) => {
          let icon = commit.new ? "upload" : "circle-filled";
          return __spreadProps(__spreadValues({}, commit), {
            icon
          });
        });
      }
    },
    created() {
      this.$emit("paginate", {
        page: this.page,
        limit: this.limit
      });
    }
  };
  const __cssModules$1 = {};
  var __component__$1 = /* @__PURE__ */ normalizeComponent(__vue2_script$1, render$1, staticRenderFns$1, false, __vue2_injectStyles$1, null, null, null);
  function __vue2_injectStyles$1(context) {
    for (let o in __cssModules$1) {
      this[o] = __cssModules$1[o];
    }
  }
  __component__$1.options.__file = "src/components/CommitsList.vue";
  var CommitsList = /* @__PURE__ */ function() {
    return __component__$1.exports;
  }();
  var render = function() {
    var _vm = this;
    var _h = _vm.$createElement;
    var _c = _vm._self._c || _h;
    return _c("k-inside", [_c("k-view", [_c("k-header", [_vm._v("Version Control")]), _c("k-grid", { attrs: { "gutter": "medium" } }, [_c("k-column", { attrs: { "width": "1/3" } }, [_c("changes-list", { attrs: { "title": "Unstaged", "data": this.unstaged } }, [this.unstaged.length ? _c("k-button-group", { attrs: { "slot": "action" }, slot: "action" }, [_c("k-button", { attrs: { "icon": "add" }, on: { "click": _vm.add } }, [_vm._v("Add")])], 1) : _vm._e()], 1)], 1), _c("k-column", { attrs: { "width": "1/3" } }, [_c("changes-list", { attrs: { "title": "Staged", "data": this.staged } }, [this.staged.length ? _c("k-button-group", { attrs: { "slot": "action" }, slot: "action" }, [_c("k-button", { attrs: { "icon": "circle-filled" }, on: { "click": function($event) {
      return _vm.$refs.commitDialog.open();
    } } }, [_vm._v("Commit")])], 1) : _vm._e()], 1), _c("k-dialog", { ref: "commitDialog", attrs: { "theme": "positive" }, on: { "submit": function($event) {
      return _vm.$refs.commitForm.submit();
    } } }, [_c("k-form", { ref: "commitForm", attrs: { "fields": {
      message: {
        type: "text",
        label: "Message",
        required: true
      }
    } }, on: { "submit": _vm.commit }, model: { value: _vm.commitData, callback: function($$v) {
      _vm.commitData = $$v;
    }, expression: "commitData" } })], 1)], 1), _c("k-column", { attrs: { "width": "1/3" } }, [_c("commits-list", { attrs: { "data": _vm.logData }, on: { "paginate": _vm.paginateLog } }, [_c("k-button-group", { attrs: { "slot": "action" }, slot: "action" }, [_c("k-button", { attrs: { "icon": "download", "disabled": !_vm.canPull }, on: { "click": _vm.pull } }, [_vm._v(" " + _vm._s(_vm.isPulling ? "Pulling\u2026" : "Pull") + " ")]), _c("k-button", { attrs: { "icon": "upload", "theme": "positive", "disabled": !_vm.canPush }, on: { "click": _vm.push } }, [_vm._v(" " + _vm._s(_vm.isPushing ? "Pushing\u2026" : "Push") + " ")])], 1)], 1)], 1)], 1)], 1)], 1);
  };
  var staticRenderFns = [];
  render._withStripped = true;
  const __vue2_script = {
    components: {
      ChangesList,
      CommitsList
    },
    data() {
      return {
        staged: [],
        unstaged: [],
        commitData: {
          message: null
        },
        logData: null,
        logPgn: null,
        isPulling: false,
        isPushing: false
      };
    },
    computed: {
      canPull() {
        return !this.isPushing && !this.isPulling;
      },
      canPush() {
        var _a;
        return !this.isPushing && !this.isPulling && ((_a = this.logData) == null ? void 0 : _a.new);
      }
    },
    created() {
      this.$api.get("git/status").then((entries) => {
        this.updateStatus(entries);
      }).catch((error) => {
        this.$store.dispatch("notification/error", error);
      });
    },
    methods: {
      updateStatus(entries) {
        this.staged = [];
        this.unstaged = [];
        entries.forEach((entry) => {
          if (entry.unstaged) {
            this.unstaged.push({
              file: entry.file,
              mode: entry.unstaged
            });
          }
          if (entry.staged && entry.staged !== "?") {
            this.staged.push({
              file: entry.file,
              mode: entry.staged
            });
          }
        });
      },
      add() {
        this.$api.post("git/add").then(() => {
          return this.$api.get("git/status");
        }).then((entries) => {
          this.updateStatus(entries);
        });
      },
      commit() {
        this.$api.post("git/commit", this.commitData).then(() => {
          this.$refs.commitDialog.close();
          return this.$api.get("git/status");
        }).then((entries) => {
          this.commitData.message = null;
          this.updateStatus(entries);
        }).catch((error) => {
          this.$refs.commitDialog.error(error.message);
        }).then(() => {
          this.listCommits();
        });
      },
      paginateLog(data) {
        this.logPgn = {
          page: data.page,
          limit: data.limit
        };
        this.listCommits();
      },
      listCommits() {
        return this.$api.get("git/log", this.logPgn).then((data) => {
          this.logData = data;
        });
      },
      push() {
        this.isPushing = true;
        this.$api.post("git/push").then(() => {
          return this.listCommits();
        }).catch((error) => {
          this.$store.dispatch("notification/error", error);
        }).then(() => {
          this.isPushing = false;
        });
      },
      pull() {
        this.isPulling = true;
        this.$api.get("git/pull").then(() => {
          return this.listCommits();
        }).catch((error) => {
          this.$store.dispatch("notification/error", error);
        }).then(() => {
          this.isPulling = false;
        });
      }
    }
  };
  const __cssModules = {};
  var __component__ = /* @__PURE__ */ normalizeComponent(__vue2_script, render, staticRenderFns, false, __vue2_injectStyles, null, null, null);
  function __vue2_injectStyles(context) {
    for (let o in __cssModules) {
      this[o] = __cssModules[o];
    }
  }
  __component__.options.__file = "src/components/GitView.vue";
  var GitView = /* @__PURE__ */ function() {
    return __component__.exports;
  }();
  panel.plugin("oblik/git", {
    sections: {
      git: GitSection
    },
    components: {
      "k-git-view": GitView
    }
  });
})();
