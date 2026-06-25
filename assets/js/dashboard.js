console.log(rmDashboard);
console.log('rmTracker:', rmTracker);

let visitsChart = null;
let countriesChart = null;
let sourcesChart = null;

const ctx =
    document.getElementById(
        'rm-visits-chart'
    );

if (
    ctx &&
    rmDashboard?.visits
) {

    const labels =
        rmDashboard.visits
            .map(
                item => item.dia
            );

    const data =
        rmDashboard.visits
            .map(
                item =>
                    Number(
                        item.total
                    )
            );

    visitsChart =
        new Chart(
            ctx,
            {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Visitas',
                            data
                        }
                    ]
                }
            }
        );
}

function createSimpleChart(
    canvasId,
    chart,
    type,
    labels,
    values
)
{
    const canvas =
        document.getElementById(
            canvasId
        );

    if (!canvas) {
        return null;
    }

    if (chart) {
        chart.destroy();
    }

    return new Chart(
        canvas,
        {
            type,
            data: {

                labels,

                datasets: [
                    {
                        data: values
                    }
                ]
            }
        }
    );
}

countriesChart =
    createSimpleChart(
        'rm-countries-chart',
        countriesChart,
        'pie',
        [],
        []
    );

sourcesChart =
    createSimpleChart(
        'rm-sources-chart',
        sourcesChart,
        'pie',
        [],
        []
    );

const period =
    document.getElementById(
        'rm-period'
    );

if (period) {

    period.addEventListener(
        'change',
        loadDashboard
    );

}

function formatTime(
    seconds
)
{
    seconds =
        Number(seconds);

    const minutes =
        Math.floor(
            seconds / 60
        );

    const remaining =
        seconds % 60;

    return (
        String(minutes)
            .padStart(2, '0')
        +
        ':'
        +
        String(remaining)
            .padStart(2, '0')
    );
}

async function loadDashboard()
{
    const value =
        document
            .getElementById(
                'rm-period'
            )
            .value;

    const response =
        await fetch(
            rmTracker.apiUrl +
            '/dashboard-data?period=' +
            value,
            {
                credentials: 'same-origin',
                headers: {
                    'X-WP-Nonce':
                        rmTracker.nonce
                }
            }
        );
    const data =
        await response.json();

    if (data.countries) {

    const labels =
        data.countries.map(
            country => country.pais
        );

    const values =
        data.countries.map(
            country =>
                Number(
                    country.total
                )
        );

    countriesChart =
        createSimpleChart(
            'rm-countries-chart',
            countriesChart,
            document.getElementById(
                'rm-countries-type'
            ).value,
            labels,
            values
        );
    }

    if (data.sources) {

        const labels =
            data.sources.map(
                source => source.source
            );

        const values =
            data.sources.map(
                source =>
                    Number(
                        source.total
                    )
            );

        sourcesChart =
            createSimpleChart(
                'rm-sources-chart',
                sourcesChart,
                document.getElementById(
                    'rm-sources-type'
                ).value,
                labels,
                values
            );
    }

    console.log(data);


    document.getElementById(
        'rm-period-visits'
    ).textContent =
        data.summary.visitas_hoy;

    document.getElementById(
        'rm-period-users'
    ).textContent =
        data.summary.usuarios_unicos;

    document.getElementById(
        'rm-period-pdfs'
    ).textContent =
        data.summary.pdf_opens;

    document.getElementById(
        'rm-active-users'
    ).textContent =
        data.summary.visitantes_activos;

    const countries =
        document.getElementById(
            'rm-countries-list'
        );

    if (
        countries &&
        data.countries
    ) {

        countries.innerHTML =
            data.countries
                .map(
                    country =>
                        `
                        <li>
                            ${country.pais}
                            -
                            ${country.total}
                        </li>
                        `
                )
                .join('');
    }

    const sources =
        document.getElementById(
            'rm-sources-list'
        );

    if (
        sources &&
        data.sources
    ) {

        sources.innerHTML =
            data.sources
                .map(
                    source =>
                        `
                        <li>
                            ${source.source}
                            -
                            ${source.total}
                        </li>
                        `
                )
                .join('');
    }

    const pages =
        document.getElementById(
            'rm-pages-list'
        );

    if (
        pages &&
        data.top_pages
    ) {

        pages.innerHTML =
            data.top_pages
                .map(
                    page =>
                        `
                        <li>
                            <span>
                                ${page.label}
                            </span>

                            <strong>
                                ${page.total}
                            </strong>
                        </li>
                        `
                )
                .join('');
    }

    const resources =
        document.getElementById(
            'rm-resources-list'
        );

    if (
        resources &&
        data.top_resources
    ) {

        resources.innerHTML =
            data.top_resources
                .map(
                    resource =>
                        `
                        <li>
                            <span>
                                ${resource.recurso}
                            </span>

                            <strong>
                                ${resource.total}
                            </strong>
                        </li>
                        `
                )
                .join('');
    }

    const pageTime =
    document.getElementById(
        'rm-page-time-list'
    );

    if (
        pageTime &&
        data.avg_page_time
    ) {

        pageTime.innerHTML =
            data.avg_page_time
                .map(
                    page =>
                        `
                        <li>
                            <span>
                                ${page.label ?? page.pagina}
                            </span>

                            <strong>
                                ${formatTime(
                                    page.promedio
                                )}
                            </strong>
                        </li>
                        `
                )
                .join('');
    }

    if (
        visitsChart &&
        data.visits
    ) {

        visitsChart.data.labels =
            data.visits.map(
                item => item.dia
            );

        visitsChart.data.datasets[0].data =
            data.visits.map(
                item =>
                    Number(
                        item.total
                    )
            );

        visitsChart.update();
    }

}

const countriesType =
    document.getElementById(
        'rm-countries-type'
    );

if (countriesType) {

    countriesType.addEventListener(
        'change',
        loadDashboard
    );
}

const sourcesType =
    document.getElementById(
        'rm-sources-type'
    );

if (sourcesType) {

    sourcesType.addEventListener(
        'change',
        loadDashboard
    );
}

loadDashboard();