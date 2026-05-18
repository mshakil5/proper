@extends('admin.pages.master')
@section('title', 'POS — Point of Sale')
@section('content')
    <style>
        :root {
            --pos-bg: #0f0f0f;
            --pos-surface: #1a1a1a;
            --pos-surface2: #242424;
            --pos-border: #2e2e2e;
            --pos-accent: #ff5a00;
            --pos-accent-dim: rgba(255, 90, 0, .15);
            --pos-text: #f0f0f0;
            --pos-muted: #888;
            --pos-success: #22c55e;
            --pos-danger: #ef4444;
            --pos-radius: 14px;
            --c0: #ff5a00;
            --c1: #3b82f6;
            --c2: #22c55e;
            --c3: #a855f7;
            --c4: #f59e0b;
            --c5: #ec4899;
            --c6: #14b8a6;
            --c7: #ef4444;
            --c8: #8b5cf6;
            --c9: #06b6d4;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: var(--pos-bg) !important;
            font-size: 15px;
        }

        .pos-shell {
            display: grid;
            grid-template-columns: 1fr 400px;
            height: calc(100vh - 100px);
            background: var(--pos-bg);
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .pos-main {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border-right: 1px solid var(--pos-border);
        }

        .pos-steps {
            display: flex;
            align-items: center;
            gap: 0;
            background: var(--pos-surface);
            border-bottom: 1px solid var(--pos-border);
            padding: 0 16px;
            flex-shrink: 0;
            overflow-x: auto;
        }

        .pos-step-tab {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 14px 18px;
            font-size: 14px;
            font-weight: 700;
            color: var(--pos-muted);
            cursor: pointer;
            border-bottom: 3px solid transparent;
            white-space: nowrap;
            transition: all .2s;
            user-select: none;
        }

        .pos-step-tab .step-num {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--pos-border);
            color: var(--pos-muted);
            font-size: 12px;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .2s;
        }

        .pos-step-tab.active {
            color: var(--pos-accent);
            border-bottom-color: var(--pos-accent);
        }

        .pos-step-tab.active .step-num {
            background: var(--pos-accent);
            color: #fff;
        }

        .pos-step-tab.done {
            color: var(--pos-success);
        }

        .pos-step-tab.done .step-num {
            background: var(--pos-success);
            color: #fff;
        }

        .step-arrow {
            color: var(--pos-border);
            font-size: 18px;
            padding: 0 4px;
        }

        .pos-step-panel {
            display: none;
            flex: 1;
            overflow: hidden;
            flex-direction: column;
        }

        .pos-step-panel.active {
            display: flex;
        }

        .step1-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            padding: 40px;
            height: 100%;
            align-content: center;
        }

        .order-type-card {
            border-radius: 20px;
            padding: 40px 20px;
            text-align: center;
            cursor: pointer;
            transition: all .2s;
            border: 3px solid transparent;
        }

        .order-type-card:hover {
            transform: translateY(-3px);
        }

        .order-type-card.selected {
            transform: translateY(-3px);
        }

        .order-type-card .icon {
            font-size: 52px;
            margin-bottom: 12px;
            display: block;
        }

        .order-type-card .label {
            font-size: 20px;
            font-weight: 900;
            color: #fff;
        }

        .order-type-card .sub {
            font-size: 14px;
            color: rgba(255, 255, 255, .7);
            margin-top: 6px;
        }

        .order-type-card[data-type="collection"] {
            background: rgba(34, 197, 94, .2);
            border-color: #22c55e;
        }

        .order-type-card[data-type="collection"]:hover,
        .order-type-card[data-type="collection"].selected {
            background: rgba(34, 197, 94, .35);
        }

        .order-type-card[data-type="delivery"] {
            background: rgba(59, 130, 246, .2);
            border-color: #3b82f6;
        }

        .order-type-card[data-type="delivery"]:hover,
        .order-type-card[data-type="delivery"].selected {
            background: rgba(59, 130, 246, .35);
        }

        .order-type-card[data-type="walkin"] {
            background: rgba(245, 158, 11, .2);
            border-color: #f59e0b;
        }

        .order-type-card[data-type="walkin"]:hover,
        .order-type-card[data-type="walkin"].selected {
            background: rgba(245, 158, 11, .35);
        }

        .step2-body {
            padding: 24px;
            flex: 1;
            overflow-y: auto;
        }

        .step2-body h3 {
            font-size: 17px;
            font-weight: 800;
            color: var(--pos-text);
            margin-bottom: 16px;
        }

        .pos-label {
            font-size: 13px;
            font-weight: 700;
            color: var(--pos-muted);
            margin-bottom: 6px;
            display: block;
        }

        .pos-input {
            width: 100%;
            background: var(--pos-surface2);
            border: 1.5px solid var(--pos-border);
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 15px;
            color: var(--pos-text);
            outline: none;
            margin-bottom: 12px;
            transition: border-color .15s;
        }

        .pos-input:focus {
            border-color: var(--pos-accent);
        }

        .pos-input::placeholder {
            color: var(--pos-muted);
        }

        .pos-input.error {
            border-color: var(--pos-danger);
        }

        .form-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .cust-suggestions {
            position: absolute;
            z-index: 1000;
            background: var(--pos-surface2);
            border: 1.5px solid var(--pos-accent);
            border-radius: 10px;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            top: calc(100% + 2px);
            left: 0;
        }

        .cust-suggestion-item {
            padding: 10px 14px;
            font-size: 14px;
            color: var(--pos-text);
            cursor: pointer;
            border-bottom: 1px solid var(--pos-border);
        }

        .cust-suggestion-item:last-child {
            border-bottom: none;
        }

        .cust-suggestion-item:hover {
            background: var(--pos-accent-dim);
            color: var(--pos-accent);
        }

        .cust-suggestion-item .cust-sug-name {
            font-weight: 700;
        }

        .cust-suggestion-item .cust-sug-sub {
            font-size: 12px;
            color: var(--pos-muted);
        }

        .or-divider {
            text-align: center;
            color: var(--pos-muted);
            font-size: 13px;
            font-weight: 700;
            margin: 12px 0;
            position: relative;
        }

        .or-divider::before,
        .or-divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 40%;
            height: 1px;
            background: var(--pos-border);
        }

        .or-divider::before {
            left: 0;
        }

        .or-divider::after {
            right: 0;
        }

        .select2-container--default .select2-selection--single {
            background: var(--pos-surface2) !important;
            border: 1.5px solid var(--pos-border) !important;
            border-radius: 10px !important;
            height: 46px !important;
            color: var(--pos-text) !important;
            margin-bottom: 12px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--pos-text) !important;
            line-height: 42px !important;
            padding-left: 14px !important;
            font-size: 15px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 10px !important;
        }

        .select2-dropdown {
            background: var(--pos-surface2) !important;
            border: 1.5px solid var(--pos-border) !important;
            border-radius: 10px !important;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            background: var(--pos-surface) !important;
            border: 1px solid var(--pos-border) !important;
            color: var(--pos-text) !important;
            border-radius: 6px !important;
            padding: 6px 10px;
            font-size: 14px;
        }

        .select2-container--default .select2-results__option {
            color: var(--pos-text) !important;
            padding: 10px 14px !important;
            font-size: 14px;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background: var(--pos-accent) !important;
        }

        .select2-container {
            width: 100% !important;
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            gap: 14px;
            padding: 20px;
            overflow-y: auto;
            flex: 1;
        }

        .cat-card {
            border-radius: 16px;
            border: none;
            padding: 28px 16px;
            text-align: center;
            cursor: pointer;
            transition: all .2s;
            color: #fff;
            position: relative;
            overflow: hidden;
            min-height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cat-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, .25);
            border-radius: 16px;
        }

        .cat-card:hover {
            transform: translateY(-3px) scale(1.02);
        }

        .cat-card.selected {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 0 0 3px #fff;
        }

        .cat-card .cat-icon {
            font-size: 40px;
            display: block;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .cat-card .cat-name {
            font-size: 1.85rem;
            font-weight: 800;
            line-height: 1.2;
            text-transform: uppercase;
            text-shadow: 0 2px 8px rgba(0, 0, 0, .6);
            position: relative;
            z-index: 1;
        }

        .cat-card .cat-count {
            font-size: 12px;
            color: rgba(255, 255, 255, .8);
            margin-top: 4px;
            position: relative;
            z-index: 1;
        }

        .product-grid-step {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 16px;
            padding: 24px;
            overflow-y: auto;
            flex: 1;
            align-content: start;
        }

        .prod-btn {
            border-radius: 14px;
            border: none;
            padding: 24px 16px;
            text-align: center;
            cursor: pointer;
            transition: all .15s;
            color: #fff;
            position: relative;
            overflow: hidden;
            min-height: 140px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .prod-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 14px;
        }

        .prod-btn:hover {
            transform: translateY(-4px) scale(1.03);
        }

        .prod-btn .prod-badge {
            font-size: 12px;
            background: rgba(255, 255, 255, .3);
            color: #fff;
            padding: 4px 10px;
            border-radius: 20px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .prod-btn .prod-name {
            font-size: 17.5px;
            font-weight: 800;
            line-height: 1.25;
            text-align: center;
        }

        .options-body {
            padding: 20px;
            overflow-y: auto;
            flex: 1;
        }

        .option-group-pos {
            margin-bottom: 20px;
        }

        .option-group-title-pos {
            font-size: 14px;
            font-weight: 800;
            color: var(--pos-muted);
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
        }

        .option-btns-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 8px;
        }

        .opt-btn {
            padding: 16px 12px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            transition: all .15s;
            text-align: center;
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            position: relative;
            overflow: hidden;
            min-height: 68px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .opt-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, .3);
        }

        .opt-btn span {
            position: relative;
            z-index: 1;
            line-height: 1.3;
        }

        .opt-btn:hover {
            transform: scale(1.03);
        }

        .opt-btn.selected {
            box-shadow: 0 0 0 3px #fff inset;
        }

        .opt-btn.option-error {
            box-shadow: 0 0 0 3px var(--pos-danger) inset !important;
        }

        .attr-choice-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 20px;
        }

        .attr-choice-btn {
            padding: 18px 12px;
            border-radius: 12px;
            border: 2px solid var(--pos-border);
            background: var(--pos-surface);
            cursor: pointer;
            text-align: center;
            transition: all .15s;
        }

        .attr-choice-btn:hover {
            border-color: var(--pos-accent);
        }

        .attr-choice-btn.selected {
            border-color: var(--pos-accent);
            background: var(--pos-accent-dim);
        }

        .attr-choice-btn .attr-label {
            font-size: 15px;
            font-weight: 800;
            color: var(--pos-text);
        }

        .attr-choice-btn .attr-sub {
            font-size: 13px;
            color: var(--pos-muted);
            margin-top: 4px;
        }

        .sauce-group-grid {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .sauce-item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            border-radius: 10px;
            border: 2px solid var(--pos-border);
            background: var(--pos-surface);
        }

        .sauce-item-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--pos-text);
        }

        .sauce-item-price {
            font-size: 13px;
            color: var(--pos-muted);
        }

        .sauce-qty-ctrl {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sauce-ctrl-btn {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: 1.5px solid var(--pos-border);
            background: var(--pos-surface2);
            color: var(--pos-text);
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .sauce-ctrl-btn:hover {
            background: var(--pos-accent);
            border-color: var(--pos-accent);
            color: #fff;
        }

        .time-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 8px;
            margin-bottom: 20px;
        }

        .time-btn {
            padding: 13px 10px;
            border-radius: 10px;
            border: 2px solid var(--pos-border);
            background: var(--pos-surface);
            cursor: pointer;
            text-align: center;
            transition: all .15s;
            font-size: 14px;
            font-weight: 700;
            color: var(--pos-text);
        }

        .time-btn:hover {
            border-color: var(--pos-accent);
        }

        .time-btn.selected {
            border-color: var(--pos-accent);
            background: var(--pos-accent-dim);
            color: var(--pos-accent);
        }

        .pos-textarea {
            width: 100%;
            background: var(--pos-surface2);
            border: 1.5px solid var(--pos-border);
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 15px;
            color: var(--pos-text);
            outline: none;
            resize: none;
            margin-bottom: 12px;
        }

        .pos-textarea:focus {
            border-color: var(--pos-accent);
        }

        .pos-nav-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            border-top: 1px solid var(--pos-border);
            background: var(--pos-surface);
            flex-shrink: 0;
            /* margin-bottom: 20px; */
        }

        .btn-nav {
            padding: 11px 24px;
            border-radius: 10px;
            border: none;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all .15s;
        }

        .btn-nav-back {
            background: var(--pos-surface2);
            color: var(--pos-text);
            border: 1.5px solid var(--pos-border);
        }

        .btn-nav-back:hover {
            border-color: var(--pos-accent);
            color: var(--pos-accent);
        }

        .btn-nav-next {
            background: var(--pos-accent);
            color: #fff;
        }

        .btn-nav-next:hover {
            background: #e04e00;
        }

        .btn-nav-next:disabled {
            background: var(--pos-border);
            color: var(--pos-muted);
            cursor: not-allowed;
        }

        .btn-nav-place {
            background: var(--pos-success);
            color: #fff;
        }

        .btn-nav-place:hover {
            background: #16a34a;
        }

        .btn-nav-place:disabled {
            background: var(--pos-border);
            color: var(--pos-muted);
            cursor: not-allowed;
        }

        .step-hint {
            font-size: 13px;
            color: var(--pos-muted);
            font-weight: 600;
        }

        .pos-cart {
            display: flex;
            flex-direction: column;
            background: var(--pos-surface);
            overflow: hidden;
        }

        .cart-header {
            padding: 14px 16px;
            border-bottom: 1px solid var(--pos-border);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .cart-header h4 {
            font-size: 16px;
            font-weight: 800;
            color: var(--pos-text);
            margin: 0;
        }

        .btn-clear-cart {
            padding: 5px 12px;
            border-radius: 8px;
            border: 1.5px solid var(--pos-border);
            background: none;
            color: var(--pos-muted);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-clear-cart:hover {
            border-color: var(--pos-danger);
            color: var(--pos-danger);
        }

        .cart-items {
            flex: 1;
            overflow-y: auto;
            padding: 12px;
        }

        .cart-empty {
            text-align: center;
            color: var(--pos-muted);
            padding: 40px 20px;
            font-size: 14px;
        }

        .cart-empty i {
            font-size: 36px;
            display: block;
            margin-bottom: 8px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            background: var(--pos-surface2);
            border-radius: 12px;
            margin-bottom: 8px;
            border: 1px solid var(--pos-border);
        }

        .cart-item-color {
            width: 4px;
            align-self: stretch;
            border-radius: 4px;
            flex-shrink: 0;
        }

        .cart-item-info {
            flex: 1;
            min-width: 0;
        }

        .cart-item-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--pos-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cart-item-opts {
            font-size: 12px;
            color: var(--pos-muted);
            line-height: 1.5;
            margin-top: 2px;
        }

        .cart-item-price {
            font-size: 14px;
            font-weight: 800;
            color: var(--pos-accent);
            white-space: nowrap;
        }

        .qty-ctrl {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-shrink: 0;
        }

        .qty-btn {
            width: 28px;
            height: 28px;
            border-radius: 7px;
            border: 1.5px solid var(--pos-border);
            background: var(--pos-surface);
            color: var(--pos-text);
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: 700;
            flex-shrink: 0;
        }

        .qty-btn:hover {
            background: var(--pos-accent);
            border-color: var(--pos-accent);
            color: #fff;
        }

        .qty-val {
            font-size: 14px;
            font-weight: 800;
            color: var(--pos-text);
            min-width: 20px;
            text-align: center;
        }

        .btn-remove-item {
            width: 28px;
            height: 28px;
            border-radius: 7px;
            border: none;
            background: rgba(239, 68, 68, .15);
            color: var(--pos-danger);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 13px;
            flex-shrink: 0;
        }

        .cart-summary {
            padding: 12px 16px;
            border-top: 1px solid var(--pos-border);
            flex-shrink: 0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: var(--pos-muted);
            margin-bottom: 5px;
        }

        .summary-row.total {
            font-size: 22px;
            font-weight: 900;
            color: var(--pos-text);
            margin-top: 8px;
        }

        .order-meta {
            padding: 10px 16px;
            background: var(--pos-surface2);
            border-top: 1px solid var(--pos-border);
            font-size: 12px;
            flex-shrink: 0;
        }

        .meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: var(--pos-border);
            border-radius: 20px;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 700;
            color: var(--pos-muted);
            margin-right: 6px;
            margin-bottom: 4px;
        }

        .meta-pill.accent {
            background: var(--pos-accent-dim);
            color: var(--pos-accent);
        }

        .qty-add-modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .6);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qty-add-box {
            background: var(--pos-surface);
            border-radius: 20px;
            padding: 28px;
            width: 320px;
            border: 1.5px solid var(--pos-border);
        }

        .qty-add-title {
            font-size: 17px;
            font-weight: 800;
            color: var(--pos-text);
            margin-bottom: 4px;
        }

        .qty-add-opts {
            font-size: 13px;
            color: var(--pos-muted);
            margin-bottom: 18px;
        }

        .qty-add-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-bottom: 20px;
        }

        .qty-add-btn {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            border: 2px solid var(--pos-border);
            background: var(--pos-surface2);
            color: var(--pos-text);
            font-size: 22px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qty-add-btn:hover {
            border-color: var(--pos-accent);
            background: var(--pos-accent-dim);
        }

        .qty-add-val {
            font-size: 32px;
            font-weight: 900;
            color: var(--pos-text);
            min-width: 50px;
            text-align: center;
        }

        .btn-add-confirm {
            width: 100%;
            height: 48px;
            background: var(--pos-accent);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            margin-bottom: 8px;
        }

        .btn-add-cancel {
            width: 100%;
            height: 40px;
            background: none;
            border: 1.5px solid var(--pos-border);
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            color: var(--pos-muted);
            cursor: pointer;
        }

        .pos-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .7);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pos-success-box {
            background: var(--pos-surface);
            border-radius: 20px;
            padding: 32px;
            width: 420px;
            text-align: center;
            border: 1.5px solid var(--pos-border);
        }

        .success-icon {
            font-size: 64px;
            margin-bottom: 12px;
        }

        .success-title {
            font-size: 24px;
            font-weight: 900;
            color: var(--pos-text);
            margin-bottom: 4px;
        }

        .success-order {
            font-size: 34px;
            font-weight: 900;
            color: var(--pos-accent);
            margin: 12px 0;
        }

        .btn-new-order {
            width: 100%;
            height: 50px;
            background: var(--pos-accent);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            margin-top: 8px;
        }

        .btn-print-again {
            width: 100%;
            height: 44px;
            background: var(--pos-surface2);
            color: var(--pos-text);
            border: 1.5px solid var(--pos-border);
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 8px;
        }

        .toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .toast {
            padding: 12px 18px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            animation: slideIn .3s ease;
        }

        .toast.success {
            background: #16a34a;
        }

        .toast.error {
            background: #dc2626;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .pos-section-title {
            font-size: 14px;
            font-weight: 800;
            color: var(--pos-muted);
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 14px;
        }

        .pos-step-header {
            padding: 14px 20px 0;
            flex-shrink: 0;
        }

        #deliveryAddressBlock {
            display: none;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid var(--pos-border);
        }
    </style>

    <div class="pos-shell">
        <div class="pos-main">
            <div class="pos-steps" id="posStepTabs">
                <div class="pos-step-tab active" data-step="1"><span class="step-num">1</span> Order Type</div>
                <span class="step-arrow">›</span>
                <div class="pos-step-tab" data-step="2"><span class="step-num">2</span> Customer</div>
                <span class="step-arrow">›</span>
                <div class="pos-step-tab" data-step="3"><span class="step-num">3</span> Category</div>
                <span class="step-arrow">›</span>
                <div class="pos-step-tab" data-step="4"><span class="step-num">4</span> Products</div>
                <span class="step-arrow">›</span>
                <div class="pos-step-tab" data-step="5"><span class="step-num">5</span> Options</div>
                <span class="step-arrow">›</span>
                <div class="pos-step-tab" data-step="6"><span class="step-num">6</span> Finalize</div>
            </div>

            <div class="pos-step-panel active" id="step1">
                <div class="step1-grid">
                    <div class="order-type-card" data-type="collection">
                        <span class="icon">🛍️</span>
                        <div class="label">Collection</div>
                        <div class="sub">Customer collects in store</div>
                    </div>
                    <div class="order-type-card" data-type="delivery">
                        <span class="icon">🛵</span>
                        <div class="label">Delivery</div>
                        <div class="sub">Deliver to address</div>
                    </div>
                    <div class="order-type-card" data-type="walkin">
                        <span class="icon">🚶</span>
                        <div class="label">Walk-in</div>
                        <div class="sub">Dine in / no customer needed</div>
                    </div>
                </div>
            </div>

            <div class="pos-step-panel" id="step2">
                <div class="step2-body">
                    <div class="pos-section-title">Find Existing Customer</div>
                    <select id="posCustomerSelect" style="width:100%;">
                        <option value="">— Search by name or phone —</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}" data-name="{{ $client->name }}"
                                data-phone="{{ $client->phone }}" data-email="{{ $client->email }}"
                                data-points="{{ $client->available_points ?? 0 }}">
                                {{ $client->name }} — {{ $client->phone }}
                            </option>
                        @endforeach
                    </select>
                    <div class="or-divider">OR</div>
                    <div class="pos-section-title">New Customer</div>
                    <div id="newCustomerInlineBlock">
                        <div class="form-row-2">
                            <div style="position:relative;">
                                <label class="pos-label">Name <span style="color:var(--pos-accent);">*</span></label>
                                <input class="pos-input" id="custName" placeholder="Full name" autocomplete="off">
                                <div id="nameSuggestions" class="cust-suggestions" style="display:none;"></div>
                            </div>
                            <div style="position:relative;">
                                <label class="pos-label">Phone <span
                                        style="color:var(--pos-muted);font-size:10px;">(optional)</span></label>
                                <input class="pos-input" id="custPhone" placeholder="07700000000" autocomplete="off">
                                <div id="phoneSuggestions" class="cust-suggestions" style="display:none;"></div>
                            </div>
                        </div>
                        <div style="position:relative;">
                            <label class="pos-label">Email <span
                                    style="color:var(--pos-muted);font-size:10px;">(optional)</span></label>
                            <input class="pos-input" id="custEmail" type="email" placeholder="customer@email.com"
                                autocomplete="off">
                            <div id="emailSuggestions" class="cust-suggestions" style="display:none;"></div>
                        </div>
                        <div id="createAccountBlock" style="margin-top: 4px;">
                            <label
                                style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;color:var(--pos-muted);margin-bottom:12px;">
                                <input type="checkbox" id="createAccountCheck" style="accent-color:var(--pos-accent);">
                                Create customer account
                            </label>
                            <div id="accountPasswordBlock" style="display:none;">
                                <label class="pos-label">Password</label>
                                <input class="pos-input" id="custPassword" type="password" value="Password123!"
                                    placeholder="Password">
                            </div>
                        </div>
                    </div>
                    <div id="deliveryAddressBlock">
                        <div class="pos-section-title">Delivery Address</div>
                        <label class="pos-label">Postcode <span style="color:var(--pos-accent);">*</span></label>
                        <input class="pos-input" id="deliveryPostcode" placeholder="e.g. LN5 8LQ">
                        <label class="pos-label">Street Address <span style="color:var(--pos-accent);">*</span></label>
                        <input class="pos-input" id="deliveryAddress" placeholder="123 Main Street">
                        <div class="form-row-2">
                            <div>
                                <label class="pos-label">City <span style="color:var(--pos-accent);">*</span></label>
                                <input class="pos-input" id="deliveryCity" placeholder="Lincoln">
                            </div>
                            <div>
                                <label class="pos-label">Address Line 2</label>
                                <input class="pos-input" id="deliveryAddress2" placeholder="Flat / Apt (optional)">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pos-step-panel" id="step3">
                <div class="pos-step-header">
                    <div class="pos-section-title">Choose a Category</div>
                </div>
                <div class="category-grid" id="categoryGrid">
                    @php
                        $catColors = [
                            '#ff5a00',
                            '#3b82f6',
                            '#22c55e',
                            '#a855f7',
                            '#f59e0b',
                            '#ec4899',
                            '#14b8a6',
                            '#ef4444',
                            '#8b5cf6',
                            '#06b6d4',
                        ];
                    @endphp

                    @foreach ($categories as $ci => $cat)
                        <div class="cat-card" data-cat-id="{{ $cat->id }}"
                            style="background: {{ $catColors[$ci % count($catColors)] }};">

                            <div class="cat-name">
                                {{ $loop->iteration }}.
                                {{ $cat->name }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="pos-step-panel" id="step4">
                <div class="pos-step-header" style="padding-bottom:10px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <div class="pos-section-title" id="step4CategoryLabel">Products</div>
                        <input type="text" id="productSearch" class="pos-input" placeholder="🔍 Search..."
                            style="width:200px;margin-bottom:0;">
                    </div>
                </div>
                <div class="product-grid-step" id="productGrid">
                    @foreach ($categories as $ci => $cat)
                        @foreach ($cat->products as $product)
                            <div class="prod-btn" data-color="{{ $ci % 10 }}" data-cat="{{ $cat->id }}"
                                data-id="{{ $product->id }}" data-name="{{ strtolower($product->title) }}"
                                data-has-options="{{ $product->options()->exists() ? 1 : 0 }}"
                                data-has-attr="{{ $product->has_attribute ? 1 : 0 }}"
                                style="display:none; background: {{ $catColors[$ci % count($catColors)] }};">

                                @if ($product->options()->exists())
                                    <div class="prod-badge">+ Options</div>
                                @endif

                                <div class="prod-name">
                                    {{ $loop->iteration }}.
                                    {{ $product->title }}
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>

            <div class="pos-step-panel" id="step5">
                <div class="pos-step-header">
                    <div class="pos-section-title" id="step5ProductLabel">Choose Options</div>
                </div>
                <div class="options-body" id="optionsBody">
                    <div style="color:var(--pos-muted);font-size:14px;">Select a product first</div>
                </div>
            </div>

            <div class="pos-step-panel" id="step6">
                <div class="step2-body">
                    <div class="pos-section-title">Time Slot</div>
                    <div class="time-grid" id="timeSlotGrid"></div>
                    <div class="pos-section-title" style="margin-top:8px;">Order Notes <span
                            style="font-weight:400;font-size:12px;color:var(--pos-muted);">(optional)</span></div>
                    <textarea class="pos-textarea" id="posNotes" rows="3" placeholder="Special instructions, allergies..."></textarea>
                </div>
            </div>

            <div class="pos-nav-bar">
                <button class="btn-nav btn-nav-back" id="btnBack" style="display:none;">← Back</button>
                <div class="step-hint" id="stepHint">Select order type to begin</div>
                <div style="display:flex;gap:10px;">
                    <button class="btn-nav btn-nav-next" id="btnNext" disabled>Next →</button>
                    <button class="btn-nav btn-nav-place" id="btnPlace" style="display:none;" disabled>✅ Place
                        Order</button>
                </div>
            </div>
        </div>

        <div class="pos-cart">
            <div class="cart-header">
                <h4>🧾 Order</h4>
                <button class="btn-clear-cart" id="btnClearCart">Clear All</button>
            </div>
            <div class="order-meta" id="orderMeta">
                <span class="meta-pill" id="metaType">No type</span>
                <span class="meta-pill" id="metaCustomer">Walk-in</span>
            </div>
            <div class="cart-items" id="cartItems">
                <div class="cart-empty"><i class="ri-shopping-basket-line"></i>Cart is empty</div>
            </div>
            <div class="cart-summary">
                <div class="summary-row"><span>Subtotal</span><span id="cartSubtotal">£0.00</span></div>
                <div class="summary-row" id="deliveryRow" style="display:none;"><span>Delivery</span><span
                        id="cartDelivery">£0.00</span></div>
                <div class="summary-row total"><span>Total</span><span id="cartTotal">£0.00</span></div>
            </div>
        </div>
    </div>

    <div id="qtyModal" style="display:none;" class="qty-add-modal">
        <div class="qty-add-box">
            <div class="qty-add-title" id="qtyModalTitle">Product Name</div>
            <div class="qty-add-opts" id="qtyModalOpts"></div>
            <div class="qty-add-row">
                <button class="qty-add-btn" id="qtyMinus">−</button>
                <span class="qty-add-val" id="qtyVal">1</span>
                <button class="qty-add-btn" id="qtyPlus">+</button>
            </div>
            <button class="btn-add-confirm" id="qtyConfirm">Add to Order</button>
            <button class="btn-add-cancel" id="qtyCancel">Cancel</button>
        </div>
    </div>

    <div id="successModal" style="display:none;" class="pos-modal-overlay">
        <div class="pos-success-box">
            <div class="success-icon">✅</div>
            <div class="success-title">Order Placed!</div>
            <div style="font-size:14px;color:var(--pos-muted);">Order Number</div>
            <div class="success-order" id="successOrderNum">—</div>
            <div style="font-size:14px;color:var(--pos-muted);margin-bottom:8px;">Cash · <span
                    id="successTotal">£0.00</span></div>
            <button class="btn-print-again" id="btnPrintAgain">🖨️ Reprint Receipts</button>
            <button class="btn-new-order" id="btnNewOrder">🛒 New Order</button>
        </div>
    </div>

    <div class="toast-container" id="toastContainer"></div>

    <script id="categoriesData" type="application/json">
        @php
            $catData = [];
            foreach ($categories as $cat) {
                $products = [];
                foreach ($cat->products as $p) {
                    $optionGroups = [];
                    foreach ($p->options as $opt) {
                        $items = [];
                        foreach ($opt->items->sortBy('override_price') as $oi) {
                            $items[] = [
                                'id'        => $oi->id,
                                'title'     => $oi->product->title ?? 'Unknown',
                                'price'     => (float)($oi->override_price ?? 0),
                                'productId' => $oi->product_id,
                                'hubrise'   => $oi->hubrise_option_ref ?? '',
                            ];
                        }
                        $optionGroups[] = [
                            'id'       => $opt->id,
                            'name'     => $opt->name,
                            'type'     => $opt->type,
                            'required' => (bool)$opt->is_required,
                            'max'      => $opt->max_select ?? 1,
                            'items'    => $items,
                        ];
                    }
                    $products[] = [
                        'id'           => $p->id,
                        'title'        => $p->title,
                        'price'        => (float)$p->price,
                        'skuRef'       => $p->sku_ref ?? '',
                        'hasAttribute' => (bool)$p->has_attribute,
                        'attrName'     => $p->attribute_name ?? '',
                        'attrPrice'    => (float)($p->attribute_price ?? 0),
                        'hasOptions'   => $p->options->isNotEmpty(),
                        'options'      => $optionGroups,
                    ];
                }
                $catData[] = ['id' => $cat->id, 'name' => $cat->name, 'products' => $products];
            }
            echo json_encode($catData);
        @endphp
    </script>

    <script id="clientsData" type="application/json">
        @php
            $clientsArr = [];
            foreach ($clients as $c) {
                $clientsArr[] = [
                    'id'    => $c->id,
                    'name'  => $c->name,
                    'phone' => $c->phone,
                    'email' => $c->email,
                    'points'=> $c->available_points ?? 0,
                ];
            }
            echo json_encode($clientsArr);
        @endphp
    </script>
@endsection

@section('script')
    <script>
        $(function() {

            const categoriesData = JSON.parse(document.getElementById('categoriesData').textContent);
            const clientsData = JSON.parse(document.getElementById('clientsData').textContent);

            const SHOP_HOURS = {
                Monday: {
                    open: '16:30',
                    close: '23:30'
                },
                Tuesday: {
                    open: '16:30',
                    close: '23:30'
                },
                Wednesday: {
                    open: '16:30',
                    close: '23:30'
                },
                Thursday: {
                    open: '16:30',
                    close: '23:30'
                },
                Friday: {
                    open: '16:30',
                    close: '23:30'
                },
                Saturday: {
                    open: '16:30',
                    close: '23:30'
                },
                Sunday: {
                    open: '16:30',
                    close: '22:00'
                },
            };

            let currentStep = 1;
            let orderType = null;
            let customer = {
                id: null,
                name: 'Walk-in',
                email: '',
                phone: '',
                points: 0
            };
            let selectedCatId = null;
            let currentProduct = null;
            let currentOptions = {};
            let currentAttr = null;
            let cart = [];
            let deliveryCharge = 0;
            let selectedTime = null;
            let lastOrderData = null;

            function goToStep(n) {
                if (n < 1 || n > 6) return;
                currentStep = n;
                $('.pos-step-panel').removeClass('active');
                $('#step' + n).addClass('active');
                $('.pos-step-tab').each(function() {
                    const s = parseInt($(this).data('step'));
                    $(this).removeClass('active done');
                    if (s === n) $(this).addClass('active');
                    else if (s < n) $(this).addClass('done');
                });

                $('#btnBack').toggle(n > 1);
                $('#btnNext').toggle(n < 6);

                $('#btnPlace').hide().prop('disabled', true);

                if (n === 4) renderProducts();
                if (n === 5) renderOptions();
                if (n === 6) renderTimeSlots();

                validateStep();
                updateHint();
            }

            function updateHint() {
                const hints = {
                    1: 'Select how customer is ordering',
                    2: 'Enter or select customer details',
                    3: 'Choose a product category',
                    4: 'Select products to add (Next when done)',
                    5: 'Choose options and add to cart',
                    6: 'Select time slot then place order',
                };
                $('#stepHint').text(hints[currentStep] || '');
            }

            function validateStep() {
                const hasItems = cart.length > 0;

                let nextOk = false;
                switch (currentStep) {
                    case 1: nextOk = !!orderType; break;
                    case 2: nextOk = validateStep2(); break;
                    case 3: nextOk = !!selectedCatId; break;
                    case 4: nextOk = true; break;
                    case 5: nextOk = true; break;
                    case 6: nextOk = hasItems && !!selectedTime; break;
                }
                $('#btnNext').prop('disabled', !nextOk);

                if (currentStep === 6 && hasItems && selectedTime) {
                    $('#btnPlace').show().prop('disabled', false);
                } else if (currentStep === 6 && hasItems && !selectedTime) {
                    $('#btnPlace').show().prop('disabled', true);
                } else {
                    $('#btnPlace').hide().prop('disabled', true);
                }

                $('#btnNext').toggle(currentStep < 6);
            }

            function validateStep2() {
                if (orderType === 'walkin') return true;
                const name = $('#custName').val().trim();
                if (!name && !customer.id) return false;
                if (orderType === 'delivery') {
                    return $('#deliveryPostcode').val().trim() &&
                        $('#deliveryAddress').val().trim() &&
                        $('#deliveryCity').val().trim();
                }
                return true;
            }

            $(document).on('keydown', function(e) {
                const tag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
                const inInput = (tag === 'input' || tag === 'textarea' || tag === 'select');

                if (!inInput && (e.key === 'ArrowRight' || e.key === 'Enter')) {
                    if ($('#qtyModal').is(':visible')) {
                        if (e.key === 'Enter') $('#qtyConfirm').trigger('click');
                        return;
                    }
                    if (currentStep < 6 && !$('#btnNext').prop('disabled')) {
                        e.preventDefault();
                        $('#btnNext').trigger('click');
                    }
                }
                if (!inInput && (e.key === 'ArrowLeft')) {
                    if (currentStep > 1) {
                        e.preventDefault();
                        $('#btnBack').trigger('click');
                    }
                }
                if (!inInput && e.key === 'Enter' && currentStep === 6) {
                    if (!$('#btnPlace').prop('disabled')) {
                        e.preventDefault();
                        $('#btnPlace').trigger('click');
                    }
                }
                if ($('#qtyModal').is(':visible')) {
                    if (e.key === '+' || e.key === '=') $('#qtyPlus').trigger('click');
                    if (e.key === '-') $('#qtyMinus').trigger('click');
                }
            });

            $('#btnNext').on('click', function() {
                if (currentStep === 2) commitCustomer();
                if (currentStep < 6) goToStep(currentStep + 1);
            });
            $('#btnBack').on('click', function() {
                if (currentStep > 1) goToStep(currentStep - 1);
            });
            $('.pos-step-tab').on('click', function() {
                const n = parseInt($(this).data('step'));
                if (n < currentStep) goToStep(n);
                else if (n === 6 && cart.length > 0) goToStep(n);
            });

            $(document).on('click', '.order-type-card', function() {
                orderType = $(this).data('type');
                $('.order-type-card').removeClass('selected');
                $(this).addClass('selected');
                updateMetaPills();
                validateStep();
                if (orderType === 'walkin') {
                    customer = {
                        id: null,
                        name: 'Walk-in',
                        email: '',
                        phone: '',
                        points: 0
                    };
                    deliveryCharge = 0;
                    setTimeout(() => goToStep(3), 200);
                } else {
                    setTimeout(() => {
                        if (orderType === 'delivery') $('#deliveryAddressBlock').show();
                        else $('#deliveryAddressBlock').hide();
                        goToStep(2);
                    }, 200);
                }
            });

            $('#posCustomerSelect').select2({
                placeholder: '— Search by name or phone —',
                allowClear: true,
                dropdownParent: $('#step2'),
            });

            $('#posCustomerSelect').on('change', function() {
                const sel = $(this).find('option:selected');
                const id = $(this).val();
                if (!id) {
                    customer = {
                        id: null,
                        name: '',
                        email: '',
                        phone: '',
                        points: 0
                    };
                    $('#custName,#custPhone,#custEmail').val('');
                    $('#createAccountCheck').prop('checked', false);
                    $('#accountPasswordBlock').hide();
                    return;
                }
                customer = {
                    id: id,
                    name: sel.data('name') || '',
                    email: sel.data('email') || '',
                    phone: sel.data('phone') || '',
                    points: parseInt(sel.data('points')) || 0,
                };
                $('#custName').val(customer.name);
                $('#custPhone').val(customer.phone);
                $('#custEmail').val(customer.email);
                $('#createAccountBlock').hide();
                updateMetaPills();
                validateStep();
            });

            function buildSuggestions(field, query, suggestionsEl) {
                if (!query || query.length < 2) {
                    $(suggestionsEl).hide();
                    return;
                }
                const q = query.toLowerCase();
                const matches = clientsData.filter(c =>
                    (c.name || '').toLowerCase().includes(q) ||
                    (c.phone || '').toLowerCase().includes(q) ||
                    (c.email || '').toLowerCase().includes(q)
                ).slice(0, 6);
                if (!matches.length) {
                    $(suggestionsEl).hide();
                    return;
                }
                let html = '';
                matches.forEach(c => {
                    html += `<div class="cust-suggestion-item" data-id="${c.id}" data-name="${c.name}" data-phone="${c.phone}" data-email="${c.email}" data-points="${c.points}">
                    <div class="cust-sug-name">${c.name}</div>
                    <div class="cust-sug-sub">${c.phone} · ${c.email}</div>
                </div>`;
                });
                $(suggestionsEl).html(html).show();
            }

            function applySuggestion(el) {
                const id = $(el).data('id');
                const name = $(el).data('name');
                const phone = $(el).data('phone');
                const email = $(el).data('email');
                const points = parseInt($(el).data('points')) || 0;
                customer = {
                    id,
                    name,
                    email,
                    phone,
                    points
                };
                $('#custName').val(name);
                $('#custPhone').val(phone);
                $('#custEmail').val(email);
                $('#nameSuggestions,#phoneSuggestions,#emailSuggestions').hide();
                $('#createAccountBlock').hide();
                if ($('#posCustomerSelect option[value="' + id + '"]').length) {
                    $('#posCustomerSelect').val(id).trigger('change.select2');
                }
                updateMetaPills();
                validateStep();
            }

            $('#custName').on('input', function() {
                if (customer.id) {
                    customer.id = null;
                    $('#createAccountBlock').show();
                }
                buildSuggestions('name', $(this).val().trim(), '#nameSuggestions');
                validateStep();
            });
            $('#custPhone').on('input', function() {
                if (customer.id) {
                    customer.id = null;
                    $('#createAccountBlock').show();
                }
                buildSuggestions('phone', $(this).val().trim(), '#phoneSuggestions');
                validateStep();
            });
            $('#custEmail').on('input', function() {
                if (customer.id) {
                    customer.id = null;
                    $('#createAccountBlock').show();
                }
                buildSuggestions('email', $(this).val().trim(), '#emailSuggestions');
                validateStep();
            });

            $(document).on('click', '.cust-suggestion-item', function() {
                applySuggestion(this);
            });
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#newCustomerInlineBlock').length) {
                    $('#nameSuggestions,#phoneSuggestions,#emailSuggestions').hide();
                }
            });

            $('#createAccountCheck').on('change', function() {
                $('#accountPasswordBlock').toggle(this.checked);
            });

            $('#custName,#custPhone,#custEmail,#deliveryPostcode,#deliveryAddress,#deliveryCity').on('input',
                function() {
                    validateStep();
                });

            function commitCustomer() {
                if (orderType === 'walkin') {
                    customer = {
                        id: null,
                        name: 'Walk-in',
                        email: '',
                        phone: '',
                        points: 0
                    };
                    return;
                }
                if (!customer.id) {
                    const name = $('#custName').val().trim();
                    customer.name = name || 'Guest';
                    customer.phone = $('#custPhone').val().trim();
                    customer.email = $('#custEmail').val().trim();

                    if ($('#createAccountCheck').is(':checked') && name) {
                        const data = {
                            first_name: name.split(' ')[0],
                            last_name: name.split(' ').slice(1).join(' ') || 'Customer',
                            email: customer.email || (name.replace(/\s/g, '').toLowerCase() + '@pos.local'),
                            phone: customer.phone || '00000000000',
                            password: $('#custPassword').val() || 'Password123!',
                        };
                        $.ajax({
                            url: '{{ route('admin.pos.quick-customer') }}',
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: data,
                            success: function(res) {
                                customer.id = res.id;
                                showToast('Customer account created!', 'success');
                            }
                        });
                    }
                }
                deliveryCharge = (orderType === 'delivery') ? 2.50 : 0;
                updateMetaPills();
                updateCartTotals();
            }

            $(document).on('click', '.cat-card', function() {
                selectedCatId = parseInt($(this).data('cat-id'));
                $('.cat-card').removeClass('selected');
                $(this).addClass('selected');
                validateStep();
                setTimeout(() => goToStep(4), 150);
            });

            function renderProducts() {
                const cat = categoriesData.find(c => c.id === selectedCatId);
                if (cat) $('#step4CategoryLabel').text(cat.name + ' — Products');
                $('.prod-btn').hide();
                $('.prod-btn[data-cat="' + selectedCatId + '"]').show();
            }

            $('#productSearch').on('input', function() {
                const q = $(this).val().toLowerCase().trim();
                $('.prod-btn[data-cat="' + selectedCatId + '"]').each(function() {
                    $(this).toggle(!q || $(this).data('name').includes(q));
                });
            });

            $(document).on('click', '.prod-btn', function() {
                const id = parseInt($(this).data('id'));
                const cat = categoriesData.find(c => c.id === selectedCatId);
                if (!cat) return;
                currentProduct = cat.products.find(p => p.id === id);
                if (!currentProduct) return;
                currentOptions = {};
                currentAttr = null;

                if (!currentProduct.hasOptions && !currentProduct.hasAttribute) {
                    openQtyModal(currentProduct.title, '', function(qty) {
                        addToCart(currentProduct, qty, {}, null);
                        goToStep(6);
                    });
                } else {
                    goToStep(5);
                }
            });

            function isComboKebab(title) {
                return title.toLowerCase().indexOf('combo kebab') !== -1;
            }

            function isHouseSpecialKebab(title) {
                return title.toLowerCase().indexOf('house special kebab') !== -1;
            }

            function renderOptions() {
                if (!currentProduct) return;
                $('#step5ProductLabel').text(currentProduct.title);

                const title = currentProduct.title;
                const isCombo = isComboKebab(title);
                const isHSK = isHouseSpecialKebab(title);
                const optColors = ['#ff5a00', '#3b82f6', '#22c55e', '#a855f7', '#f59e0b', '#ec4899', '#14b8a6', '#ef4444', '#8b5cf6', '#06b6d4'];

                let html = '';

                if (currentProduct.hasAttribute) {
                    html += '<div class="pos-section-title">Choose Option</div>';
                    html += '<div class="attr-choice-row">';
                    html += `<div class="attr-choice-btn" data-attr="standalone">
                        <div class="attr-label">On Its Own</div>
                        <div class="attr-sub">£${currentProduct.price.toFixed(2)}</div>
                    </div>`;
                    html += `<div class="attr-choice-btn" data-attr="with_options">
                        <div class="attr-label">${currentProduct.attrName}</div>
                        <div class="attr-sub">£${(currentProduct.price + currentProduct.attrPrice).toFixed(2)}</div>
                    </div>`;
                    html += '</div>';
                    html += '<div id="optionGroupsContainer" style="display:none;">';
                } else {
                    html += '<div id="optionGroupsContainer">';
                }

                if (currentProduct.options && currentProduct.options.length) {
                    currentProduct.options.forEach(function(grp, gi) {
                        const color = optColors[gi % optColors.length];

                        if (grp.name.toLowerCase().includes('sauce options')) {
                            html += `<div class="option-group-pos" data-group-id="${grp.id}" data-type="sauce" data-required="${grp.required ? 1 : 0}" data-max="${grp.max}">`;
                            html += `<div class="option-group-title-pos">
                                <span>${grp.name}${grp.required ? ' <span style="color:var(--pos-accent);">*</span>' : ''}</span>
                                <span style="font-size:12px;color:var(--pos-muted);">Max 3 total</span>
                            </div>`;
                            html += '<div class="sauce-group-grid">';
                            grp.items.forEach(function(item, ii) {
                                const scolor = optColors[ii % optColors.length];
                                html += `<div class="sauce-item-row" style="border-color:${scolor}30;">
                                    <div>
                                        <div class="sauce-item-name">
                                            <span style="opacity:0.85; font-weight:700; margin-right:6px;">${ii + 1}.</span>
                                            ${item.title}
                                        </div>
                                        ${item.price > 0 ? `<div class="sauce-item-price">+£${item.price.toFixed(2)}</div>` : ''}
                                    </div>
                                    <div class="sauce-qty-ctrl">
                                        <button type="button" class="sauce-ctrl-btn pos-sauce-minus" data-group="${grp.id}" data-item-id="${item.id}" data-title="${item.title}" data-price="${item.price}" data-hubrise="${item.hubrise}">−</button>
                                        <span class="sauce-qty-display" id="sauceQty_${grp.id}_${item.id}" style="font-size:16px;font-weight:900;color:var(--pos-text);min-width:24px;text-align:center;">0</span>
                                        <button type="button" class="sauce-ctrl-btn pos-sauce-plus" data-group="${grp.id}" data-item-id="${item.id}" data-title="${item.title}" data-price="${item.price}" data-hubrise="${item.hubrise}">+</button>
                                    </div>
                                </div>`;
                            });
                            html += '</div></div>';
                            return;
                        }

                        html += `<div class="option-group-pos" data-group-id="${grp.id}" data-type="${grp.type}" data-required="${grp.required ? 1 : 0}" data-max="${grp.max}">`;
                        html += `<div class="option-group-title-pos">
                            <span>${grp.name}${grp.required ? ' <span style="color:var(--pos-accent);">*</span>' : ''}</span>
                            <span style="font-size:12px;color:var(--pos-muted);">${grp.required ? '⚠️ Required' : 'Optional'}${grp.type !== 'single' && grp.max > 0 ? ' · Max ' + grp.max : ''}</span>
                        </div>`;
                        html += '<div class="option-btns-grid">';

                        grp.items.forEach(function(item, ii) {
                            const c = optColors[ii % optColors.length];
                            html += `<div class="opt-btn" data-group="${grp.id}" data-type="${grp.type}" data-item-id="${item.id}"
                                data-title="${item.title}" data-price="${item.price}" data-product-id="${item.productId}" data-hubrise="${item.hubrise}"
                                style="background:${c};">
                                <span>
                                    <span style="opacity:0.85; font-weight:700; margin-right:6px;">${ii + 1}.</span>
                                    ${item.title}
                                    ${item.price > 0 ? ' <small>+£' + item.price.toFixed(2) + '</small>' : ''}
                                </span>
                            </div>`;
                        });

                        html += '</div></div>';
                    });
                }

                html += '</div>';

                html += `<div style="margin-top:16px;">
                    <button class="btn-nav btn-nav-next" id="btnAddToOrder" style="width:100%;height:50px;font-size:16px;">Add to Order</button>
                </div>`;

                $('#optionsBody').html(html);

                if (isCombo) initComboKebabFilter();
                validateOptionsAndEnable();
            }

            function initComboKebabFilter() {
                const sections = [];
                $('.option-group-pos[data-group-id]').each(function() {
                    sections.push($(this));
                });
                if (sections.length < 2) return;
                for (let i = 0; i < sections.length; i++) {
                    for (let j = i + 1; j < sections.length; j++) {
                        const aIds = sections[i].find('.opt-btn').map(function() {
                            return $(this).data('item-id');
                        }).get().sort().join(',');
                        const bIds = sections[j].find('.opt-btn').map(function() {
                            return $(this).data('item-id');
                        }).get().sort().join(',');
                        if (aIds === bIds) bindLinkedOptionGroups(sections[i], sections[j]);
                    }
                }
            }

            function bindLinkedOptionGroups(sA, sB) {
                function syncFilter(changed, other) {
                    const checked = changed.find('.opt-btn.selected');
                    const checkedId = checked.length ? checked.data('item-id') : null;
                    other.find('.opt-btn').show();
                    if (checkedId) {
                        const conflict = other.find('.opt-btn[data-item-id="' + checkedId + '"]');
                        conflict.hide();
                        if (conflict.hasClass('selected')) {
                            conflict.removeClass('selected');
                            const gid = other.data('group-id');
                            if (currentOptions[gid]) {
                                currentOptions[gid] = currentOptions[gid].filter(i => i.title !== conflict.data(
                                    'title'));
                                if (!currentOptions[gid].length) delete currentOptions[gid];
                            }
                        }
                    }
                    validateOptionsAndEnable();
                }
                sA.on('click', '.opt-btn', function() {
                    setTimeout(() => syncFilter(sA, sB), 10);
                });
                sB.on('click', '.opt-btn', function() {
                    setTimeout(() => syncFilter(sB, sA), 10);
                });
            }

            $(document).on('click', '.attr-choice-btn', function() {
                currentAttr = $(this).data('attr');
                $('.attr-choice-btn').removeClass('selected');
                $(this).addClass('selected');
                if (currentAttr === 'with_options') {
                    $('#optionGroupsContainer').slideDown(200);
                } else {
                    $('#optionGroupsContainer').slideUp(200);
                    currentOptions = {};
                }
                validateOptionsAndEnable();
            });

            $(document).on('click', '.opt-btn', function() {
                const groupId = $(this).data('group');
                const type = $(this).data('type');
                const group = $('.option-group-pos[data-group-id="' + groupId + '"]');
                const max = parseInt(group.data('max')) || 1;

                if (type === 'single') {
                    group.find('.opt-btn').removeClass('selected');
                    $(this).addClass('selected');
                    currentOptions[groupId] = [{
                        title: $(this).data('title'),
                        price: parseFloat($(this).data('price')) || 0,
                        productId: $(this).data('product-id'),
                        hubriseOptionRef: $(this).data('hubrise') || ''
                    }];
                } else {
                    if ($(this).hasClass('selected')) {
                        $(this).removeClass('selected');
                        if (currentOptions[groupId]) {
                            currentOptions[groupId] = currentOptions[groupId].filter(i => i.title !== $(
                                this).data('title'));
                            if (!currentOptions[groupId].length) delete currentOptions[groupId];
                        }
                    } else {
                        const selected = group.find('.opt-btn.selected').length;
                        if (selected >= max) {
                            showToast('Max ' + max + ' selections allowed', 'error');
                            return;
                        }
                        $(this).addClass('selected');
                        if (!currentOptions[groupId]) currentOptions[groupId] = [];
                        currentOptions[groupId].push({
                            title: $(this).data('title'),
                            price: parseFloat($(this).data('price')) || 0,
                            productId: $(this).data('product-id'),
                            hubriseOptionRef: $(this).data('hubrise') || ''
                        });
                    }
                }
                validateOptionsAndEnable();
            });

            $(document).on('click', '.pos-sauce-plus', function() {
                const grpId = $(this).data('group');
                const itemId = $(this).data('item-id');
                const title = $(this).data('title');
                const price = parseFloat($(this).data('price')) || 0;
                const hubrise = $(this).data('hubrise') || '';
                const dispEl = $('#sauceQty_' + grpId + '_' + itemId);
                let cur = parseInt(dispEl.text()) || 0;
                let total = 0;
                $('[id^="sauceQty_' + grpId + '_"]').each(function() {
                    total += parseInt($(this).text()) || 0;
                });
                if (cur >= 3 || total >= 3) {
                    showToast('Max 3 sauce selections', 'error');
                    return;
                }
                cur++;
                dispEl.text(cur);
                if (!currentOptions[grpId]) currentOptions[grpId] = [];
                currentOptions[grpId].push({
                    title,
                    price,
                    productId: null,
                    hubriseOptionRef: hubrise
                });
                validateOptionsAndEnable();
            });

            $(document).on('click', '.pos-sauce-minus', function() {
                const grpId = $(this).data('group');
                const itemId = $(this).data('item-id');
                const title = $(this).data('title');
                const dispEl = $('#sauceQty_' + grpId + '_' + itemId);
                let cur = parseInt(dispEl.text()) || 0;
                if (cur <= 0) return;
                cur--;
                dispEl.text(cur);
                if (currentOptions[grpId]) {
                    const idx = currentOptions[grpId].findIndex(i => i.title === title);
                    if (idx !== -1) currentOptions[grpId].splice(idx, 1);
                    if (!currentOptions[grpId].length) delete currentOptions[grpId];
                }
                validateOptionsAndEnable();
            });

            function validateOptionsAndEnable() {
                let valid = true;
                if (currentProduct && currentProduct.hasAttribute && !currentAttr) valid = false;
                if (currentAttr === 'with_options' || (!currentProduct?.hasAttribute)) {
                    $('.option-group-pos[data-required="1"]').each(function() {
                        const gid = $(this).data('group-id');
                        if (!currentOptions[gid] || !currentOptions[gid].length) valid = false;
                    });
                }
                $('#btnAddToOrder').prop('disabled', !valid);
            }

            $(document).on('click', '#btnAddToOrder', function() {
                if (currentProduct.hasAttribute && !currentAttr) {
                    showToast('Please choose an option', 'error');
                    return;
                }
                let valid = true,
                    firstError = null;
                if (currentAttr === 'with_options' || !currentProduct.hasAttribute) {
                    $('.option-group-pos[data-required="1"]').each(function() {
                        const gid = $(this).data('group-id');
                        if (!currentOptions[gid] || !currentOptions[gid].length) {
                            valid = false;
                            $(this).find('.option-btns-grid .opt-btn').addClass('option-error');
                            if (!firstError) firstError = $(this);
                        }
                    });
                }
                if (!valid) {
                    if (firstError) firstError[0].scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    showToast('Please select required options', 'error');
                    return;
                }
                $('.opt-btn').removeClass('option-error');

                let optsSummary = '';
                Object.values(currentOptions).forEach(arr => arr.forEach(o => {
                    optsSummary += o.title + (o.price > 0 ? ` (+£${o.price.toFixed(2)})` : '') +
                        ', ';
                }));
                optsSummary = optsSummary.replace(/, $/, '');

                openQtyModal(currentProduct.title, optsSummary, function(qty) {
                    addToCart(currentProduct, qty, currentOptions, currentAttr);
                    currentProduct = null;
                    currentOptions = {};
                    currentAttr = null;
                    goToStep(6);
                });
            });

            function openQtyModal(title, opts, callback) {
                let qty = 1;
                $('#qtyModalTitle').text(title);
                $('#qtyModalOpts').text(opts);
                $('#qtyVal').text(qty);
                $('#qtyModal').show();

                $('#qtyPlus').off('click').on('click', function() {
                    qty++;
                    $('#qtyVal').text(qty);
                });
                $('#qtyMinus').off('click').on('click', function() {
                    if (qty > 1) {
                        qty--;
                        $('#qtyVal').text(qty);
                    }
                });
                $('#qtyCancel').off('click').on('click', function() {
                    $('#qtyModal').hide();
                });
                $('#qtyModal').off('click.overlay').on('click.overlay', function(e) {
                    if (e.target === this) $('#qtyModal').hide();
                });
                $('#qtyConfirm').off('click').on('click', function() {
                    $('#qtyModal').hide();
                    callback(qty);
                });
            }

            function addToCart(product, qty, options, attr) {
                let optionPrice = 0,
                    attributePrice = 0;
                if (attr === 'with_options' && product.hasAttribute) attributePrice = product.attrPrice;
                Object.values(options).forEach(arr => arr.forEach(o => {
                    optionPrice += o.price;
                }));
                const unitPrice = product.price + optionPrice + attributePrice;
                const optHash = JSON.stringify(options) + (attr || '');
                const existing = cart.find(i => i.productId === product.id && i.optHash === optHash);
                if (existing) {
                    existing.qty += qty;
                } else {
                    cart.push({
                        productId: product.id,
                        skuRef: product.skuRef,
                        title: product.title,
                        price: unitPrice,
                        qty,
                        options,
                        attribute: attr === 'with_options',
                        attributePrice,
                        type: Object.keys(options).length ? 'custom' : 'direct',
                        optHash,
                        catColor: getCatColor(product.id),
                    });
                }
                renderCart();
                showToast(product.title + ' added!', 'success');
                validateStep();
            }

            function getCatColor(productId) {
                const colors = ['#ff5a00', '#3b82f6', '#22c55e', '#a855f7', '#f59e0b', '#ec4899', '#14b8a6',
                    '#ef4444', '#8b5cf6', '#06b6d4'
                ];
                for (let ci = 0; ci < categoriesData.length; ci++) {
                    if (categoriesData[ci].products.find(p => p.id === productId)) return colors[ci % colors
                    .length];
                }
                return '#ff5a00';
            }

            function renderCart() {
                if (!cart.length) {
                    $('#cartItems').html(
                        '<div class="cart-empty"><i class="ri-shopping-basket-line"></i>Cart is empty</div>');
                    updateCartTotals();
                    return;
                }
                let html = '';
                cart.forEach((item, idx) => {
                    let optsHtml = '';
                    if (item.options && Object.keys(item.options).length) {
                        Object.values(item.options).forEach(arr => arr.forEach(o => {
                            optsHtml += o.title + (o.price > 0 ? ` +£${o.price.toFixed(2)}` :
                                '') + '<br>';
                        }));
                    }
                    html += `<div class="cart-item">
                    <div class="cart-item-color" style="background:${item.catColor};"></div>
                    <div class="cart-item-info">
                        <div class="cart-item-name">${item.title}</div>
                        ${optsHtml ? '<div class="cart-item-opts">' + optsHtml + '</div>' : ''}
                        <div class="cart-item-price">£${(item.price * item.qty).toFixed(2)}</div>
                    </div>
                    <div class="qty-ctrl">
                        <button class="qty-btn cart-minus" data-idx="${idx}">−</button>
                        <span class="qty-val">${item.qty}</span>
                        <button class="qty-btn cart-plus" data-idx="${idx}">+</button>
                    </div>
                    <button class="btn-remove-item cart-remove" data-idx="${idx}"><i class="ri-delete-bin-line"></i></button>
                </div>`;
                });
                $('#cartItems').html(html);
                updateCartTotals();
            }

            $(document).on('click', '.cart-plus', function() {
                cart[$(this).data('idx')].qty++;
                renderCart();
                validateStep();
            });
            $(document).on('click', '.cart-minus', function() {
                const idx = $(this).data('idx');
                if (cart[idx].qty > 1) cart[idx].qty--;
                else cart.splice(idx, 1);
                renderCart();
                validateStep();
            });
            $(document).on('click', '.cart-remove', function() {
                cart.splice($(this).data('idx'), 1);
                renderCart();
                validateStep();
            });
            $('#btnClearCart').on('click', function() {
                cart = [];
                renderCart();
                validateStep();
            });

            function updateCartTotals() {
                const subtotal = cart.reduce((s, i) => s + i.price * i.qty, 0);
                const total = subtotal + deliveryCharge;
                $('#cartSubtotal').text('£' + subtotal.toFixed(2));
                $('#cartTotal').text('£' + total.toFixed(2));
                if (deliveryCharge > 0) {
                    $('#deliveryRow').show();
                    $('#cartDelivery').text('£' + deliveryCharge.toFixed(2));
                } else {
                    $('#deliveryRow').hide();
                }
            }

            function updateMetaPills() {
                const typeMap = {
                    collection: '🛍️ Collection',
                    delivery: '🛵 Delivery',
                    walkin: '🚶 Walk-in'
                };
                $('#metaType').text(orderType ? typeMap[orderType] : 'No type').toggleClass('accent', !!orderType);
                $('#metaCustomer').text('👤 ' + (customer.name || 'Walk-in').split(' ')[0]);
            }

            function renderTimeSlots() {
                if (orderType === 'walkin') {
                    selectedTime = 'ASAP';
                    $('#timeSlotGrid').html('<div style="padding:16px 0;font-size:18px;font-weight:800;color:var(--pos-success);">🚶 Walk-in</div>');
                    validateStep();
                    return;
                }
                const slots = generateTimeSlots();
                let html = '';
                if (!slots.length) {
                    html = '<p style="color:var(--pos-muted);font-size:14px;">No available slots for today.</p>';
                } else {
                    slots.forEach(s => {
                        html +=
                            `<div class="time-btn${selectedTime === s.value ? ' selected' : ''}" data-time="${s.value}">${s.label}</div>`;
                    });
                }
                $('#timeSlotGrid').html(html);
            }

            $(document).on('click', '.time-btn', function() {
                selectedTime = $(this).data('time');
                $('.time-btn').removeClass('selected');
                $(this).addClass('selected');
                validateStep();
            });

            function generateTimeSlots() {
                const now = new Date();
                const dayName = now.toLocaleDateString('en-GB', {
                    weekday: 'long'
                });
                const hours = SHOP_HOURS[dayName];
                const [openH, openM] = hours.open.split(':').map(Number);
                const [closeH, closeM] = hours.close.split(':').map(Number);
                let cursor = new Date(now.getFullYear(), now.getMonth(), now.getDate(), openH, openM);
                const close = new Date(now.getFullYear(), now.getMonth(), now.getDate(), closeH, closeM);
                if (now > cursor) {
                    const rounded = Math.ceil(now.getMinutes() / 20) * 20;
                    if (rounded >= 60) cursor = new Date(now.getFullYear(), now.getMonth(), now.getDate(), now
                        .getHours() + 1, 0);
                    else cursor = new Date(now.getFullYear(), now.getMonth(), now.getDate(), now.getHours(),
                        rounded);
                }
                const slots = [];
                const fmt = d => d.toLocaleTimeString('en-GB', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });
                while (cursor < close) {
                    const end = new Date(cursor.getTime() + 20 * 60000);
                    if (end > close) break;
                    slots.push({
                        value: fmt(cursor) + '-' + fmt(end),
                        label: fmt(cursor) + ' – ' + fmt(end)
                    });
                    cursor = end;
                }
                return slots;
            }

            $('#btnPlace').on('click', function() {
                if (!cart.length || !selectedTime) return;

                let custFirstName = 'Walk-in',
                    custLastName = 'Customer',
                    custEmail = '',
                    custPhone = '';
                if (orderType !== 'walkin') {
                    const nameParts = (customer.name || '').trim().split(' ');
                    custFirstName = nameParts[0] || 'Customer';
                    custLastName = nameParts.slice(1).join(' ') || 'POS';
                    custEmail = customer.email || '';
                    custPhone = customer.phone || '';
                }

                const orderData = {
                    customer: {
                        firstName: custFirstName,
                        lastName: custLastName,
                        email: custEmail,
                        phone: custPhone
                    },
                    customer_id: customer.id,
                    delivery: {
                        type: orderType === 'delivery' ? 'delivery' : 'collection',
                        time: selectedTime,
                        postcode: orderType === 'delivery' ? $('#deliveryPostcode').val().trim() : '',
                    },
                    address: orderType === 'delivery' ? $('#deliveryAddress').val().trim() : '',
                    address2: orderType === 'delivery' ? $('#deliveryAddress2').val().trim() : '',
                    city: orderType === 'delivery' ? $('#deliveryCity').val().trim() : '',
                    cart: cart.map(i => ({
                        productId: i.productId,
                        quantity: i.qty,
                        type: i.type,
                        options: i.options || null,
                        attribute: i.attribute || false,
                        attributePrice: i.attributePrice || 0,
                    })),
                    paymentMethod: 'cash',
                    notes: $('#posNotes').val().trim(),
                };

                $('#btnPlace').prop('disabled', true).text('Placing...');

                $.ajax({
                    url: '{{ route('admin.pos.place-order') }}',
                    type: 'POST',
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: JSON.stringify(orderData),
                    success: function(res) {
                        $('#btnPlace').prop('disabled', false).text('✅ Place Order');
                        if (res.success) {
                            const subtotal = cart.reduce((s, i) => s + i.price * i.qty, 0);
                            const total = subtotal + deliveryCharge;
                            $('#successOrderNum').text(res.orderNumber);
                            $('#successTotal').text('£' + total.toFixed(2));

                            lastOrderData = {
                                orderNumber: res.orderNumber,
                                orderId: res.orderId,
                                customer: {
                                    ...customer,
                                    firstName: custFirstName,
                                    lastName: custLastName,
                                    email: custEmail,
                                    phone: custPhone
                                },
                                orderType,
                                deliveryType: orderData.delivery.type,
                                address: orderData.address,
                                address2: orderData.address2,
                                city: orderData.city,
                                postcode: orderData.delivery.postcode,
                                cart: JSON.parse(JSON.stringify(cart)),
                                deliveryCharge,
                                subtotal,
                                total,
                                time: selectedTime,
                                notes: orderData.notes,
                                placedAt: new Date(),
                            };

                            $('#successModal').show();
                            attemptPrint(lastOrderData);
                        } else {
                            showToast(res.message || 'Error placing order', 'error');
                        }
                    },
                    error: function(xhr) {
                        $('#btnPlace').prop('disabled', false).text('✅ Place Order');
                        const msg = xhr.responseJSON?.message || xhr.responseJSON?.error || 'Error placing order';
                        showError(msg);
                    }
                });
            });

            function attemptPrint(data) {
                const win = window.open('/admin/pos/receipt/' + data.orderId, '_blank');
                if (win) {
                    win.onload = function() {
                        win.print();
                        setTimeout(() => win.close(), 500);
                    };
                }
            }

            $('#btnPrintAgain').on('click', function() {
                if (lastOrderData) attemptPrint(lastOrderData);
                else showToast('No order data to print', 'error');
            });

            $('#btnNewOrder').on('click', function() {
                $('#successModal').hide();
                resetAll();
            });

            function resetAll() {
                cart = [];
                orderType = null;
                selectedCatId = null;
                currentProduct = null;
                currentOptions = {};
                currentAttr = null;
                deliveryCharge = 0;
                selectedTime = null;
                customer = {
                    id: null,
                    name: 'Walk-in',
                    email: '',
                    phone: '',
                    points: 0
                };
                $('#posCustomerSelect').val('').trigger('change');
                $('#custName,#custPhone,#custEmail,#deliveryPostcode,#deliveryAddress,#deliveryCity,#deliveryAddress2,#posNotes')
                    .val('');
                $('#createAccountCheck').prop('checked', false);
                $('#accountPasswordBlock').hide();
                $('#createAccountBlock').show();
                $('.order-type-card').removeClass('selected');
                renderCart();
                updateMetaPills();
                goToStep(1);
            }

            function showToast(msg, type) {
                const id = 'toast-' + Date.now();
                $('#toastContainer').append(`<div class="toast ${type}" id="${id}">${msg}</div>`);
                setTimeout(() => $('#' + id).remove(), 2800);
            }

            goToStep(1);
        });
    </script>
@endsection