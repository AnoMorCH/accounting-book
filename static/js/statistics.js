class ChartHandler {
  htmlElement;
  config;
  #data;

  constructor(acceptedReviewsAmount, declinedReviewsAmount) {
    this.htmlElement = this.#getHtmlElement();
    this.#data = this.#getData(acceptedReviewsAmount, declinedReviewsAmount);
    this.config = this.#getConfig(this.#data);
  }

  #getHtmlElement() {
    const htmlElement = document.getElementById("statistics-chart");
    return htmlElement;
  }

  #getData(acceptedReviewsAmount, declinedReviewsAmount) {
    const Utils = ChartUtils.init();
    const labels = [
      "Статистика за всё время"
    ];
    const data = {
      labels: labels,
      datasets: [
        {
          label: "Кол-во одобренных отзывов, шт",
          data: [acceptedReviewsAmount],
          borderColor: Utils.CHART_COLORS.green,
          backgroundColor: Utils.transparentize(Utils.CHART_COLORS.green, 0.5),
          borderWidth: 2
        },
        {
          label: "Кол-во заблокированных отзывов, шт",
          data: [declinedReviewsAmount],
          borderColor: Utils.CHART_COLORS.red,
          backgroundColor: Utils.transparentize(Utils.CHART_COLORS.red, 0.5),
          borderWidth: 2
        }
      ]
    };
    return data;
  }

  #getConfig(data) {
    const config = {
      type: "bar",
      data: data,
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: "top",
          },
          title: {
            display: true,
            text: "Статистика"
          }
        }
      } 
    }
    return config;
  }
}

fetch("http://localhost/au/controller/customer/review/api/get_statistics.php")
  .then((response) => {
    if (!response.ok) {
      throw new Error("Ошибка! Данные не переданы");
    }
    return response.json();
  })
  .then((jsonAnswer) => {
    const chartHandler = new ChartHandler(
      jsonAnswer["accepted_reviews_amount"],
      jsonAnswer["declined_reviews_amount"]
    );
    const ctx = chartHandler.htmlElement; 
    const config = chartHandler.config;
    new Chart(ctx, config);
  })
  .catch((error) => {
    console.log(error);
  });
