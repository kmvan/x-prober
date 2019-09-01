import CardStore from '~components/Card/src/stores'
import { gettext } from '~components/Language/src'
import component from './components'

CardStore.addCard({
  id: 'phpExtensions',
  title: gettext('PHP extensions'),
  tinyTitle: gettext('Ext'),
  priority: 500,
  component,
})
