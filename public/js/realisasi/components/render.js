import { 
    renderCategoryRow, 
    renderItemRow, 
    renderItemLogTable,
    renderSDMDateRow,
    renderSDMDetailArea,
    renderSDMEmpty,
    renderTimelineItem,
    renderTimelineEmpty
} from './template.js';

export function renderTable(data, tbodyElement) {
    let html = '';
    
    data.forEach(category => {
        html += renderCategoryRow(category);
        
        if (category.expanded && category.children) {
            html += renderItemsRecursive(category.children, category.id, 0);
        }
    });
    
    tbodyElement.innerHTML = html;
}

function renderItemsRecursive(items, parentId, depth) {
    let html = '';

    items.forEach((item, index) => {
        item.parentId = parentId;
        html += renderItemRow(item, depth);

        if (item.expandedItem) {
            html += renderItemLogTable(item);
        }

        if (item.children && item.children.length > 0) {
            html += renderItemsRecursive(item.children, item.id, depth + 1);
        }
    });

    return html;
}

export function renderSDMTable(data, tbodyElement) {
    if (!data || data.length === 0) {
        tbodyElement.innerHTML = renderSDMEmpty();
        return;
    }

    let html = '';
    
    data.forEach(item => {
        html += renderSDMDateRow(item);
        
        if (item.expanded) {
            html += renderSDMDetailArea(item);
        }
    });
    
    tbodyElement.innerHTML = html;
}

export function renderLogTimeline(groupedLogs, container) {
    if (!container) return;

    if (!groupedLogs || groupedLogs.length === 0) {
        container.innerHTML = renderTimelineEmpty();
        return;
    }

    const lineHtml = '<div class="absolute left-[9px] top-2 bottom-0 w-[3px] bg-[#0f172a] rounded-full timeline-line"></div>';
    
    let itemsHtml = '';
    groupedLogs.forEach(group => {
        itemsHtml += renderTimelineItem(group);
    });

    container.innerHTML = lineHtml + itemsHtml;
}
