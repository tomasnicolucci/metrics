console.log(
    rmDashboard
);

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

    new Chart(
        ctx,
        {
            type: 'line',

            data: {

                labels,

                datasets: [
                    {
                        label:
                            'Visitas',

                        data
                    }
                ]
            }
        }
    );
}