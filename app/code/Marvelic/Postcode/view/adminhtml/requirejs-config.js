var config = {
  map: {
    '*': {      
      thailand: 'Marvelic_Postcode/js/Thailand',
    }
  },
  paths: {
    jql: 'Marvelic_Postcode/js/dependencies/JQL.min',
    typeahead: 'Marvelic_Postcode/js/dist/typeahead.jquery',
    thailand: 'Marvelic_Postcode/js/Thailand'
  },
  shim: {
    'jql': {
      exports: 'JQL'
    },
    'typeahead': {
      deps: ['jquery']
    },
    'thailand': {
      deps: ['jquery','jql','typeahead'],
      exports: 'Thailand'
    }
  }
    // config: {
    //   mixins: {
    //     'Magento_Ui/js/lib/validation/validator': {
    //         'Marvelic_Postcode/js/validation-mixin': true
    //     }
    //   }
    // }
};