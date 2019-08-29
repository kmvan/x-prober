import CardStore from '~components/Card/src/stores'
import { gettext } from '~components/Language/src'
import component from './components'

CardStore.addCard({
  id: 'myInfo',
  title: gettext('My information'),
  tinyTitle: gettext('Mine'),
  priority: 900,
  component,
})
