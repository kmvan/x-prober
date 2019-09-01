import React, { Component } from 'react'
import { observer } from 'mobx-react'
import CardGrid from '~components/Card/src/components/card-grid'
import { gettext } from '~components/Language/src'
import store from '../stores'
import ProgressBar from '~components/ProgressBar/src/components'

@observer
class MemBuffers extends Component {
  public render() {
    const { max, value } = store.memBuffers

    return (
      <CardGrid title={gettext('Memory buffers')} tablet={[1, 2]}>
        <ProgressBar value={value} max={max} isCapacity={true} />
      </CardGrid>
    )
  }
}

export default MemBuffers
