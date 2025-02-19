import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useToast } from '@/components/ui/toast/use-toast'

const API_URL = 'https://localhost/api'

export interface Order {
  id: number
  reference: string
  status: { name: string }
  createdAt: Date
  updatedAt: Date
  package: Package
  statusHistory?: StatusHistory[]
}

export interface StatusHistory {
  status: string
  changeDate: Date
}

export interface Package {
  id: number
  trackingNumber: string
  locations: PackageLocation[]
}

export interface PackageLocation {
  latitude: number
  longitude: number
  timestamp: Date
}

export const useOrdersStore = defineStore('orders', {
  state: () => ({
    orders: [] as Order[],
    selectedOrder: null as Order | null,
    statusHistory: [] as StatusHistory[],
    isLoading: false,
    error: null as string | null,
    pagination: {
      page: 1,
      pageSize: 10,
      total: 0
    },
    eventSource: null as EventSource | null
  }),

  getters: {
    totalPages: (state) => Math.ceil(state.pagination.total / state.pagination.pageSize),
    lastStatus: (state) => state.statusHistory[state.statusHistory.length - 1],
    lastLocation: (state) => state.selectedOrder?.package.locations[state.selectedOrder?.package.locations.length - 1]
  },

  actions: {
    async fetchOrders(params?: { page?: number; pageSize?: number }) {
      this.isLoading = true
      this.error = null
      try {
        const searchParams = new URLSearchParams({
          page: String(params?.page || this.pagination.page),
          pageSize: String(params?.pageSize || this.pagination.pageSize)
        })

        const response = await fetch(`${API_URL}/orders?${searchParams}`, {
          headers: { 'Accept': 'application/json' }
        })

        if (!response.ok) throw new Error('Erreur lors de la récupération des commandes')

        const data = await response.json()
        this.orders = data.orders
        this.pagination = {
          page: data.page,
          pageSize: data.pageSize,
          total: data.total
        }
      } catch (e) {
        this.error = e instanceof Error ? e.message : 'Une erreur est survenue'
      } finally {
        this.isLoading = false
      }
    },

    async createOrder(orderData: Partial<Order>) {
      this.isLoading = true
      this.error = null
      try {
        const response = await fetch(`${API_URL}/orders`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify(orderData)
        })

        if (!response.ok) throw new Error('Erreur lors de la création de la commande')

        await this.fetchOrders()
        return await response.json()
      } catch (e) {
        this.error = e instanceof Error ? e.message : 'Une erreur est survenue'
        throw e
      } finally {
        this.isLoading = false
      }
    },

    async updateOrderStatus(status: string) {
      if (!this.selectedOrder) return

      this.isLoading = true
      this.error = null
      try {
        const response = await fetch(`${API_URL}/orders/${this.selectedOrder.id}/status`, {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({ status })
        })

        if (!response.ok) throw new Error('Erreur lors de la mise à jour du statut')

        return await response.json()
      } catch (e) {
        this.error = e instanceof Error ? e.message : 'Une erreur est survenue'
        throw e
      } finally {
        this.isLoading = false
      }
    },

    async fetchStatusHistory() {
      if (!this.selectedOrder) return

      this.isLoading = true
      this.error = null
      try {
        const response = await fetch(`${API_URL}/orders/${this.selectedOrder.id}/status-history`)
        if (!response.ok) throw new Error('Erreur lors de la récupération de l\'historique')

        this.statusHistory = await response.json()
      } catch (e) {
        this.error = e instanceof Error ? e.message : 'Une erreur est survenue'
      } finally {
        this.isLoading = false
      }
    },

    async selectOrder(order: Order | null) {
      this.selectedOrder = order
      this.eventSource?.close()

      if (!order) {
        this.statusHistory = []
        return
      }

      this.isLoading = true
      await this.fetchStatusHistory()
      this.initMercureSubscription()
      this.isLoading = false
    },

    async refreshOrder() {
      if (!this.selectedOrder) return

      try {
        this.isLoading = true
        const selectedOrderId = this.selectedOrder.id
        const response = await fetch(`${API_URL}/orders/${selectedOrderId}`)
        if (!response.ok) throw new Error('Erreur lors de la récupération de la commande')
        const newOrder = await response.json()
        this.selectedOrder = newOrder
        await this.fetchStatusHistory()
        this.orders = this.orders.map(order => order.id === newOrder.id ? newOrder : order)
        this.isLoading = false
      } catch (e) {
        this.error = e instanceof Error ? e.message : 'Une erreur est survenue'
        this.isLoading = false
      }
    },

    async simulatePackageLocation(trackingNumber: string) {
      if (!this.selectedOrder) return;

      try {
        const response = await fetch(`${API_URL}/packages/${trackingNumber}/simulate`, {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            iterations: 10, // Nombre d'itérations souhaité
            startLatitude: 48.8566,  // Paris
            startLongitude: 2.3522,
            endLatitude: 45.7578,    // Lyon
            endLongitude: 4.8320
          })
        });

        if (!response.ok) throw new Error('Erreur lors de la simulation de la position du colis');

        return await response.json();
      } catch (e) {
        this.error = e instanceof Error ? e.message : 'Une erreur est survenue';
      }
    },

    initMercureSubscription() {
      const url = new URL(import.meta.env.VITE_MERCURE_HUB_URL)
      url.searchParams.append('topic', `order/${this.selectedOrder?.id}`)

      this.eventSource = new EventSource(url.toString(), {
        withCredentials: false
      })

      const { toast } = useToast()

      this.eventSource.onmessage = async (event) => {
        const data = JSON.parse(event.data)

        switch (data.type) {
          case 'order.status_changed':
            const order = JSON.parse(data.order)
            await this.refreshOrder()
            toast({
              title: 'Statut de la commande modifié',
              description: `La commande ${this.selectedOrder?.reference} est passée au statut ${order.status.name}`,
            })
            break
          case 'package.created':
            const orderPackage = JSON.parse(data.package)
            await this.refreshOrder()
            toast({
              title: 'Nouveau colis créé',
              description: `Colis ${orderPackage.trackingNumber} créé`,
            })
            break
          case 'package.location_updated':
            this.selectedOrder?.package.locations.push(data.data.location)
            toast({
              title: 'Package location updated',
              description: `Package ${data.data.trackingNumber} location updated (${data.data.location.latitude}, ${data.data.location.longitude})`,
            })
            break
        }
      }
    }
  }
})
