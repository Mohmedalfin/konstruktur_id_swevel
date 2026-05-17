export function flattenLeafItems(items) {
    const result = [];

    const traverse = (nodes) => {
        if (!nodes || nodes.length === 0) return;

        nodes.forEach(node => {
            const hasChildren = node.children && node.children.length > 0;

            if (hasChildren) {
                traverse(node.children);
            } else {
                result.push(node);
            }
        });
    };

    items.forEach(category => traverse(category.children || []));

    return result;
}

export function getAllLogsRecursive(items) {
    const leafItems = flattenLeafItems(items);
    const flatLogs = [];

    leafItems.forEach(item => {
        if (item.logs && item.logs.length > 0) {
            item.logs.forEach(log => {
                flatLogs.push({
                    ...log,
                    taskUraian: item.uraian,
                    satuan: item.satuan
                });
            });
        }
    });

    return flatLogs;
}

export function groupLogsByDate(flatLogs) {
    const groupedMap = flatLogs.reduce((acc, log) => {
        const dateKey = log.tanggal;
        
        if (!acc[dateKey]) {
            acc[dateKey] = {
                date: dateKey,
                tasks: [],
                photos: []
            };
        }

        acc[dateKey].tasks.push({
            uraian: log.taskUraian,
            volume: log.volumeTercapai,
            keterangan: log.keterangan
        });

        if (log.foto && Array.isArray(log.foto)) {
            acc[dateKey].photos.push(...log.foto);
        }

        return acc;
    }, {});

    const groupedArray = Object.values(groupedMap);

    groupedArray.sort((a, b) => {
        const parseDate = (dateStr) => {
            if (!dateStr) return 0;
            const parts = dateStr.split('-');
            if (parts.length === 3) {
                if (parts[0].length === 4) {
                    return new Date(dateStr).getTime();
                } else {
                    return new Date(`${parts[2]}-${parts[1]}-${parts[0]}`).getTime();
                }
            }
            return new Date(dateStr).getTime();
        };
        
        return parseDate(b.date) - parseDate(a.date);
    });

    return groupedArray;
}
