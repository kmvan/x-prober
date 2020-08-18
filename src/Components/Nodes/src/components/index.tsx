import React, { Component } from 'react'
import { observer } from 'mobx-react'
import Row from '~components/Grid/src/components/row'
import store from '../stores'
import Grid from '~components/Grid/src/components/grid'
import styled from 'styled-components'
import { OK } from '~components/Restful/src/http-status'
import { gettext } from '~components/Language/src'
import { DataProps, DataNetworkStatsProps } from '~components/Fetch/src/stores'
import { SysLoadGroup } from '~components/ServerStatus/src/components/system-load'
import ProgressBar from '~components/ProgressBar/src/components'
import template from '~components/Helper/src/components/template'
import {
  ServerStatusCpuUsageProps,
  ServerStatusUsageProps,
} from '~components/ServerStatus/src/stores'
import { GUTTER, BORDER_RADIUS } from '~components/Config/src'
import NetworksStatsItem from '~components/NetworkStats/src/components/item'
import { NetworkStatsItemProps } from '~components/NetworkStats/src/stores'
import { rgba } from 'polished'
import Alert from '~components/Helper/src/components/alert'
import Loading from '~components/Helper/src/components/loading'
import BootstrapStore from '~components/Bootstrap/src/stores'

const StyledNodeGroupId = styled.a`
  display: block;
  text-decoration: underline;
  text-align: center;
  margin-bottom: calc(${GUTTER} / 2);
  :hover {
    text-decoration: none;
  }
`
const StyledNodeGroup = styled.div`
  margin-bottom: calc(${GUTTER} / 2);
`
const StyledNodeGroupMsg = styled(StyledNodeGroup)`
  display: flex;
  justify-content: center;
`

const StyledNodeGroupNetworks = styled.div`
  border-radius: ${BORDER_RADIUS};
  background: ${({ theme }) => rgba(theme.colorDark, 0.1)};
  color: ${({ theme }) => theme.colorDark};
  padding: ${GUTTER};
  margin-bottom: ${GUTTER};
`
const StyledNodeGroupNetwork = styled.div`
  border-bottom: 1px dashed ${({ theme }) => rgba(theme.colorDark, 0.1)};
  margin-bottom: calc(${GUTTER} / 2);
  padding-bottom: calc(${GUTTER} / 2);
  &:last-child {
    margin-bottom: 0;
    border-bottom: 0;
    padding-bottom: 0;
  }
`

interface NodesNetworks {
  id: string
  networks: NetworkStatsItemProps[]
  timestamp: number
}

@observer
export default class Nodes extends Component {
  private prevNodesNetworks: NodesNetworks[] = []

  public componentDidMount() {
    const { items, itemsCount } = store

    if (!itemsCount) {
      return
    }

    for (const { id, fetchUrl } of items) {
      this.fetch({ id, fetchUrl })
    }
  }

  private fetch = async ({
    id,
    fetchUrl,
  }: {
    id: string
    fetchUrl: string
  }) => {
    const { setItem } = store

    await fetch(fetchUrl, {
      cache: 'no-cache',
      mode: 'cors',
      headers: {
        Authorization: BootstrapStore.conf.authorization,
      },
    })
      .then(async res => {
        if (res.status === OK) {
          const item = (await res.json()) as DataProps

          if (!item) {
            return
          }

          setItem({ id, isLoading: false, data: item })
          // fetch again
          setTimeout(() => {
            this.fetch({ id, fetchUrl })
          }, 1000)
        } else {
          setItem({
            id,
            isLoading: false,
            isError: true,
            errMsg: template(gettext('Fetch failed. Node returns ${code}.'), {
              code: res.status,
            }),
          })
        }
      })
      .catch(e => {
        setItem({
          id,
          isLoading: false,
          isError: true,
          errMsg: gettext('Fetch failed. Detail in Console.'),
        })
        console.log(
          template(gettext('Node [${node}] fetch failed.'), { node: id }),
          e
        )
      })
  }

  private renderSysLoad(sysLoad: number[]) {
    if (!sysLoad?.length) {
      return null
    }

    return (
      <StyledNodeGroup>
        <SysLoadGroup isCenter sysLoad={sysLoad} />
      </StyledNodeGroup>
    )
  }

  private renderCpu(cpuUsage: ServerStatusCpuUsageProps) {
    return (
      <StyledNodeGroup>
        <ProgressBar
          title={template(
            gettext(
              'idle: ${idle} \nnice: ${nice} \nsys: ${sys} \nuser: ${user}'
            ),
            cpuUsage
          )}
          value={100 - cpuUsage.idle}
          max={100}
          isCapacity={false}
          left={gettext('CPU usage')}
        />
      </StyledNodeGroup>
    )
  }

  private renderMemory({ value, max }: ServerStatusUsageProps) {
    const percent = Math.floor((value / max) * 10000) / 100

    return (
      <StyledNodeGroup>
        <ProgressBar
          title={template(gettext('Usage: ${percent}'), {
            percent: `${percent.toFixed(1)}%`,
          })}
          value={value}
          max={max}
          isCapacity
          left={gettext('Memory')}
        />
      </StyledNodeGroup>
    )
  }

  private renderSwap({ value, max }: ServerStatusUsageProps) {
    const percent = Math.floor((value / max) * 10000) / 100

    return (
      <StyledNodeGroup>
        <ProgressBar
          title={template(gettext('Usage: ${percent}'), {
            percent: `${percent.toFixed(1)}%`,
          })}
          value={value}
          max={max}
          isCapacity
          left={gettext('Swap')}
        />
      </StyledNodeGroup>
    )
  }

  private renderNetworks(
    nodeId: string,
    { networks, timestamp }: DataNetworkStatsProps
  ) {
    return networks.map(({ id, rx, tx }) => {
      if (!networks?.length) {
        return null
      }

      const prevNodeNetworks = this.prevNodesNetworks.find(
        ({ id }) => id === nodeId
      )
      const prevNodeNetwork = prevNodeNetworks?.networks.find(
        item => item.id === id
      )
      const prevRx = prevNodeNetwork?.rx || rx
      const prevTx = prevNodeNetwork?.tx || tx
      const prevTimestamp = prevNodeNetworks?.timestamp || timestamp
      let seconds = timestamp - prevTimestamp
      seconds = seconds < 1 ? 1 : seconds
      const i = this.prevNodesNetworks.findIndex(({ id }) => id === nodeId)

      if (i === -1) {
        this.prevNodesNetworks.push({
          id: nodeId,
          networks,
          timestamp,
        })
      } else {
        this.prevNodesNetworks[i] = {
          ...this.prevNodesNetworks[i],
          ...{
            networks,
            timestamp,
          },
        }
      }
      // console.log(JSON.stringify(this.prevNodesNetworks))

      return (
        <StyledNodeGroupNetwork key={id}>
          <NetworksStatsItem
            id={id}
            singleLine={false}
            totalRx={rx}
            rateRx={(rx - prevRx) / seconds}
            totalTx={tx}
            rateTx={(tx - prevTx) / seconds}
          />
        </StyledNodeGroupNetwork>
      )
    })
  }

  private renderItems() {
    const { items } = store

    return items.map(({ id, url, isLoading, isError, errMsg, data }) => {
      const idLink = <StyledNodeGroupId href={url}>{id}</StyledNodeGroupId>

      switch (true) {
        case isLoading:
          return (
            <Grid key={id} tablet={[1, 4]} mobileLg={[1, 2]}>
              {idLink}
              <StyledNodeGroupMsg>
                <Loading>{gettext('Fetching...')}</Loading>
              </StyledNodeGroupMsg>
            </Grid>
          )
        case isError:
          return (
            <Grid key={id} tablet={[1, 4]} mobileLg={[1, 2]}>
              {idLink}
              <StyledNodeGroupMsg>
                <Alert isSuccess={false} msg={errMsg} />
              </StyledNodeGroupMsg>
            </Grid>
          )
      }

      const { serverStatus, networkStats } = data

      return (
        <Grid
          key={id}
          tablet={[1, 2]}
          desktopSm={[1, 3]}
          desktopMd={[1, 4]}
          desktopLg={[1, 6]}
        >
          {idLink}
          {this.renderSysLoad(serverStatus.sysLoad)}
          {this.renderCpu(serverStatus?.cpuUsage)}
          {this.renderMemory(serverStatus?.memRealUsage)}
          {this.renderSwap(serverStatus?.swapUsage)}
          <StyledNodeGroupNetworks>
            {this.renderNetworks(id, networkStats)}
          </StyledNodeGroupNetworks>
        </Grid>
      )
    })
  }

  public render() {
    return <Row>{this.renderItems()}</Row>
  }
}
