import React, { Component } from 'react'
import { observer } from 'mobx-react'
import CardGrid from '~components/Card/src/components/card-grid'
import { gettext } from '~components/Language/src'
import store from '../stores'
import ProgressBar from '~components/ProgressBar/src/components'

@observer
export default class MemCached extends Component {
  public render() {
    const { max, value } = store.memCached

    return (
      <CardGrid
        title={gettext(
          'Cached memory is memory that Linux uses for disk caching. However, this doesn\'t count as "used" memory, since it will be freed when applications require it. Hence you don\'t have to worry if a large amount is being used.'
        )}
        name={gettext('Memory cached')}
        tablet={[1, 2]}
      >
        <ProgressBar value={value} max={max} isCapacity={true} />
      </CardGrid>
    )
  }
}
