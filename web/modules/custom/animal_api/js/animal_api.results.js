(function (once) {
  once('animal-api-results', 'body').forEach(function (body) {
    body.addEventListener('click', function (event) {
      var searchButton = event.target.closest('.animal-api-search-button');
      var importButton = event.target.closest('.animal-api-import-button');

      if (searchButton) {
        var results = document.getElementById('animal-api-results');

        if (results) {
          results.removeAttribute('hidden');
        }
      }

      if (importButton) {
        var fields = {
          'title[0][value]': importButton.dataset.animalName,
          'field_scientific_name[0][value]': importButton.dataset.animalScientificName,
          'field_lifespan[0][value]': importButton.dataset.animalLifespan,
          'field_size[0][value]': importButton.dataset.animalSize,
          'field_weight[0][value]': importButton.dataset.animalWeight,
          'field_quick_fact[0][value]': importButton.dataset.animalSlogan
        };

        Object.keys(fields).forEach(function (name) {
          var field = document.querySelector('[name="' + name + '"]');

          if (field) {
            field.value = fields[name] || '';
            field.dispatchEvent(new Event('input', {bubbles: true}));
            field.dispatchEvent(new Event('change', {bubbles: true}));
          }
        });

        var typeField = document.querySelector('select[name^="field_soort_dier"]');
        var animalClass = importButton.dataset.animalClass;
        var animalTypeLabels = {
          amphibia: 'amfibieën',
          insecta: 'insecten/geleedpotigen',
          arachnida: 'insecten/geleedpotigen',
          reptilia: 'reptielen',
          actinopterygii: 'vissen',
          chondrichthyes: 'vissen',
          aves: 'vogels',
          mammalia: 'zoogdieren'
        };
        var animalType = animalTypeLabels[animalClass.trim().toLowerCase()];

        if (typeField && animalType) {
          Array.from(typeField.options).some(function (option) {
            if (option.textContent.trim().toLowerCase() === animalType) {
              typeField.value = option.value;
              typeField.dispatchEvent(new Event('change', {bubbles: true}));
              return true;
            }

            return false;
          });
        }
      }
    });
  });
})(once);
