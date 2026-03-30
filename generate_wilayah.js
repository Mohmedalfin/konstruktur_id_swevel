const fs = require('fs');

async function build() {
    try {
        console.log("Fetching provinces...");
        const pRes = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
        const provinces = await pRes.json();
        
        let all = [];
        for(let i=0; i < provinces.length; i++) {
            let p = provinces[i];
            console.log(`Fetching regencies for ${p.name} (${i+1}/${provinces.length})...`);
            let rRes = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${p.id}.json`);
            let regencies = await rRes.json();
            all.push({
                id: p.id,
                name: p.name,
                regencies: regencies.map(r => ({ id: r.id, name: r.name }))
            });
        }
        
        fs.mkdirSync('public/assets/json', { recursive: true });
        fs.writeFileSync('public/assets/json/wilayah.json', JSON.stringify(all));
        console.log("Done generating wilayah.json!");
    } catch(e) {
        console.error(e);
    }
}
build();
