<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import Map from 'ol/Map';
import View from 'ol/View';
import TileLayer from 'ol/layer/Tile';
import OSM from 'ol/source/OSM';
import { fromLonLat } from 'ol/proj';
import VectorLayer from 'ol/layer/Vector';
import VectorSource from 'ol/source/Vector';
import { Feature } from 'ol';
import { LineString, Point } from 'ol/geom';
import { Style, Stroke, Circle, Fill } from 'ol/style';
import { type PackageLocation } from '@/stores/orders';
import { useOrdersStore } from '@/stores/orders';
import { storeToRefs } from 'pinia';
import 'ol/ol.css';

const ordersStore = useOrdersStore()
const { selectedOrder } = storeToRefs(ordersStore)

const mapElement = ref<HTMLElement>();
const map = ref<Map | null>(null);
const vectorSource = ref<VectorSource | null>(null);

const createMap = () => {
  if (!mapElement.value) return;

  // Create vector source and features for the delivery route
  const routeCoords = selectedOrder.value?.package.locations.map(coord => {
    // Assurez-vous que les coordonnées sont bien en lon/lat avant la conversion
    return fromLonLat([coord.longitude, coord.latitude]);
  }) ?? [];
  const uniqueRouteCoords = routeCoords.filter((coord, index, self) =>
    index === self.findIndex((t) => t[0] === coord[0] && t[1] === coord[1])
  );

  const routeFeature = new Feature({
    geometry: new LineString(uniqueRouteCoords)
  });

  // Create points for each stop
  const pointFeatures = selectedOrder.value?.package.locations.map(coord =>
    new Feature({
      geometry: new Point(fromLonLat([coord.longitude, coord.latitude]))
    })
  );

  // Create vector source with route and points
  vectorSource.value = new VectorSource({
    features: [routeFeature, ...(pointFeatures ?? [])]
  });

  // Style for the route line
  const routeStyle = new Style({
    stroke: new Stroke({
      color: '#0066cc',
      width: 3
    })
  });

  // Style for the points
  const pointStyle = new Style({
    image: new Circle({
      radius: 7,
      fill: new Fill({ color: '#ff3300' }),
      stroke: new Stroke({ color: '#ffffff', width: 2 })
    })
  });

  // Create vector layer
  const vectorLayer = new VectorLayer({
    source: vectorSource.value,
    style: (feature) => {
      if (feature.getGeometry() instanceof LineString) {
        return routeStyle;
      }
      return pointStyle;
    }
  });

  // Create map
  const lastLocation = selectedOrder.value?.package.locations[selectedOrder.value.package.locations.length - 1];
  map.value = new Map({
    target: mapElement.value,
    layers: [
      new TileLayer({
        source: new OSM()
      }),
      vectorLayer
    ],
    view: new View({
      center: fromLonLat([lastLocation?.longitude ?? 0, lastLocation?.latitude ?? 0]),
      zoom: 6 // Augmenter le zoom pour mieux voir les détails
    })
  });

  // Ajuster la vue pour montrer tous les points
  const extent = vectorSource.value?.getExtent();
  if (extent && map.value && lastLocation) {
    map.value.getView().fit(extent, {
      padding: [50, 50, 50, 50],
      maxZoom: 12
    });
  }
};

watch(() => ordersStore.lastLocation, (location) => {
  if (!map.value || !location) return;
  map.value.getView().setCenter(fromLonLat([location.longitude, location.latitude]));
  // Add new point to vector source
  const point = new Feature({
    geometry: new Point(fromLonLat([location.longitude, location.latitude]))
  });
  vectorSource.value?.addFeature(point);
}, { deep: true });

watch(() => selectedOrder, () => {
  if (map.value) {
    map.value.setTarget(undefined);
    map.value = null;
  }
  createMap();
}, { deep: true });

onMounted(createMap);

</script>

<template>
    <div ref="mapElement" class="w-full h-full"></div>
</template>


