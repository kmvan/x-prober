import React, { Component } from 'react'
import { observer } from 'mobx-react'
import CardGrid from '~components/Card/src/components/card-grid'
import { gettext } from '~components/Language/src'
import store from '../stores'
import ProgressBar from '~components/ProgressBar/src/components'
import { template } from 'lodash-es'

@observer
class CpuUsage extends Component {
  public render() {
    const { idle, nice, sys, user } = store.cpuUsage

    return (
      <CardGrid title={gettext('CPU usage')} tablet={[1, 1]}>
        <ProgressBar
          title={template(
            gettext(
              'idle: <%= idle %>, nice: <%= nice %>, sys: <%= sys %>, user: <%= user %>'
            )
          )({
            idle,
            nice,
            sys,
            user,
          })}
          value={100 - idle}
          max={100}
          isCapacity={false}
        />
      </CardGrid>
    )
  }
}

export default CpuUsage
